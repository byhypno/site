<?php
class ModelToolEgeserProductImporter extends Model {
    private $language_id = 0;

    public function parseFile($path, $filename) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext === 'csv') {
            $matrix = $this->parseCsv($path);
        } elseif ($ext === 'xlsx') {
            $matrix = $this->parseXlsx($path);
        } else {
            throw new Exception('Desteklenmeyen dosya tipi.');
        }
        return $this->matrixToRows($matrix);
    }

    public function analyzeRows($rows, $allow_update = false) {
        $summary = array('rows'=>0,'creates'=>0,'updates'=>0,'skips'=>0,'errors'=>0,'warnings'=>0);
        $result = array();
        $seen_models = array();
        $seen_keywords = array();

        foreach ($rows as $i => $row) {
            if ($this->rowIsEmpty($row)) continue;
            $summary['rows']++;
            $a = $this->analyzeRow($row, $i + 2, $allow_update);

            $model_key = $this->lower(trim($a['model']));
            if ($model_key !== '') {
                if (isset($seen_models[$model_key])) {
                    $a['errors'][] = 'Dosya içinde aynı model kodu birden fazla satırda var (ilk satır: ' . $seen_models[$model_key] . ').';
                } else {
                    $seen_models[$model_key] = $a['row'];
                }
            }
            $keyword_key = $this->lower(trim($a['keyword']));
            if ($keyword_key !== '') {
                if (isset($seen_keywords[$keyword_key])) {
                    $a['errors'][] = 'Dosya içinde aynı SEO URL birden fazla satırda var (ilk satır: ' . $seen_keywords[$keyword_key] . ').';
                } else {
                    $seen_keywords[$keyword_key] = $a['row'];
                }
            }

            if (!empty($a['errors'])) $a['action'] = 'error';
            if ($a['action'] === 'create') $summary['creates']++;
            elseif ($a['action'] === 'update') $summary['updates']++;
            elseif ($a['action'] === 'skip') $summary['skips']++;
            elseif ($a['action'] === 'error') $summary['errors']++;
            $summary['warnings'] += count($a['warnings']);
            $result[] = $a;
        }

        return array('rows'=>$result,'summary'=>$summary);
    }

    public function applyRows($rows, $allow_update = false) {
        $preview = $this->analyzeRows($rows, $allow_update);
        if (!empty($preview['summary']['errors'])) {
            return array('error'=>'Aktarım uygulanmadı: önizlemede ' . (int)$preview['summary']['errors'] . ' hata var. Önce dosyayı düzeltin.', 'preview'=>$preview);
        }

        $this->load->model('catalog/product');
        $created = 0; $updated = 0; $skipped = 0;
        $run_id = date('Ymd_His') . '_' . substr(sha1(microtime(true)), 0, 6);
        $this->writeLog('RUN ' . $run_id . ' START');

        foreach ($preview['rows'] as $a) {
            if ($a['action'] === 'skip') { $skipped++; continue; }
            if ($a['action'] === 'update' && !$allow_update) { $skipped++; continue; }

            $data = $this->buildProductData($a['source'], $a['product_id']);
            try {
                if ($a['action'] === 'update') {
                    $this->model_catalog_product->editProduct((int)$a['product_id'], $data);
                    $updated++;
                    $this->writeLog('UPDATE product_id=' . (int)$a['product_id'] . ' model=' . $a['model'] . ' keyword=' . $a['keyword']);
                } else {
                    $pid = $this->model_catalog_product->addProduct($data);
                    $created++;
                    $this->writeLog('CREATE product_id=' . (int)$pid . ' model=' . $a['model'] . ' keyword=' . $a['keyword']);
                }
            } catch (Exception $e) {
                $this->writeLog('ERROR row=' . (int)$a['row'] . ' model=' . $a['model'] . ' message=' . $e->getMessage());
                return array('error'=>'Aktarım satır ' . (int)$a['row'] . ' sırasında durdu: ' . $e->getMessage(), 'preview'=>$preview, 'created'=>$created, 'updated'=>$updated, 'skipped'=>$skipped);
            }
        }

        $this->writeLog('RUN ' . $run_id . ' END created=' . $created . ' updated=' . $updated . ' skipped=' . $skipped);
        return array('created'=>$created,'updated'=>$updated,'skipped'=>$skipped,'preview'=>$preview);
    }

    public function writeLog($message) {
        $dir = defined('DIR_LOGS') ? DIR_LOGS : (DIR_SYSTEM . 'storage/logs/');
        @file_put_contents(rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . 'egeser_product_import.log', '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND);
    }

    private function analyzeRow($row, $row_no, $allow_update) {
        $name = $this->field($row, array('urun_adi','ürün_adı','ürün adı','name','name(tr-tr)'));
        $model = $this->field($row, array('model','model_kodu','model kodu'));
        $keyword = $this->cleanKeyword($this->field($row, array('seo_url','seo_keyword','seo url')));
        $operation = $this->lower($this->field($row, array('islem','işlem','action')));
        $categories_raw = $this->field($row, array('kategori','category','categories'));
        $errors = array(); $warnings = array();

        if ($operation === 'skip' || $operation === 'atla') {
            return array('row'=>$row_no,'action'=>'skip','product_id'=>0,'model'=>$model,'name'=>$name,'categories'=>$categories_raw,'keyword'=>$keyword,'price'=>'','status'=>'','errors'=>array(),'warnings'=>array('Satır islem=ATLA olduğu için uygulanmayacak.'),'source'=>$row);
        }

        if ($name === '') $errors[] = 'Ürün adı boş.';
        if ($model === '') $errors[] = 'Model kodu boş.';

        $existing_by_model = $model !== '' ? $this->findProductByModel($model) : 0;
        $existing_by_keyword = $keyword !== '' ? $this->findProductByKeyword($keyword) : 0;
        if ($existing_by_model && $existing_by_keyword && $existing_by_model != $existing_by_keyword) {
            $errors[] = 'Model kodu ve SEO URL farklı mevcut ürünlere ait.';
        }
        $product_id = $existing_by_model ? $existing_by_model : $existing_by_keyword;

        if ($keyword === '') {
            if ($product_id) {
                $current_kw = $this->getProductKeyword($product_id);
                if ($current_kw !== '') $keyword = $current_kw;
            }
            if ($keyword === '' && $name !== '') {
                $keyword = $this->slugify($name);
                $warnings[] = 'SEO URL boştu; önizleme için otomatik üretildi: ' . $keyword;
            }
        }

        if ($keyword !== '') {
            $conflict = $this->getAliasConflict($keyword, $product_id);
            if ($conflict) $errors[] = 'SEO URL başka bir kayıt tarafından kullanılıyor: ' . $conflict;
        }

        $category_ids = array(); $category_names = array();
        if ($categories_raw === '') {
            $errors[] = 'Kategori boş.';
        } else {
            $parts = preg_split('/\s*[|;]\s*/u', $categories_raw, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($parts as $part) {
                if (preg_match('/^\d+(\s*,\s*\d+)*$/', trim($part))) {
                    $errors[] = 'Kategori alanında eski sayısal ID kullanılmış. Kategori adı veya SEO URL yazın: ' . $part;
                    continue;
                }
                $cat = $this->findCategory(trim($part));
                if (!$cat) {
                    $errors[] = 'Kategori bulunamadı: ' . trim($part);
                } else {
                    $category_names[] = $cat['name'];
                    foreach ($this->getCategoryPathIds($cat['category_id']) as $cid) $category_ids[$cid] = $cid;
                }
            }
        }

        $description = $this->field($row, array('aciklama_html','açıklama_html','description','description(tr-tr)'));
        $meta_title = $this->field($row, array('meta_title','meta_title(tr-tr)'));
        $meta_description = $this->field($row, array('meta_description','meta_description(tr-tr)'));
        if ($description === '' && !$product_id) $warnings[] = 'Açıklama boş.';
        if ($meta_title === '' && !$product_id) $warnings[] = 'Meta title boş; ürün adı kullanılacak.';
        if ($meta_description === '' && !$product_id) $warnings[] = 'Meta description boş.';

        $image = $this->normalizeImage($this->field($row, array('ana_gorsel','ana_görsel','image','image_name')));
        if ($image !== '' && !is_file(DIR_IMAGE . $image)) $warnings[] = 'Ana görsel sunucuda bulunamadı: ' . $image;
        $additional = $this->parseImageList($this->field($row, array('ek_gorseller','ek_görseller','additional_images')));
        foreach ($additional as $img) if (!is_file(DIR_IMAGE . $img)) $warnings[] = 'Ek görsel sunucuda bulunamadı: ' . $img;

        $price_raw = $this->field($row, array('fiyat','price'));
        $price = $price_raw === '' ? '' : $this->parsePrice($price_raw);
        if ($price_raw !== '' && $price === false) $errors[] = 'Fiyat formatı okunamadı: ' . $price_raw;
        if ($price === 0.0) $warnings[] = 'Fiyat 0: ürün sayfasında teklif odaklı fiyat alanı kullanılacak.';

        $status = $this->parseStatus($this->field($row, array('durum','status')), 1);
        $action = $product_id ? 'update' : 'create';
        if ($operation === 'create' || $operation === 'olustur' || $operation === 'oluştur') {
            if ($product_id) $errors[] = 'islem=OLUSTUR seçilmiş ancak model/SEO URL zaten mevcut.';
            $action = 'create';
        } elseif ($operation === 'update' || $operation === 'guncelle' || $operation === 'güncelle') {
            if (!$product_id) $errors[] = 'islem=GUNCELLE seçilmiş ancak eşleşen mevcut ürün bulunamadı.';
            $action = 'update';
        }
        if ($action === 'update' && !$allow_update) $warnings[] = 'Mevcut ürün bulundu; "Mevcut ürünleri güncelle" işaretli değilse uygulamada atlanacak.';

        return array(
            'row'=>$row_no,'action'=>empty($errors)?$action:'error','product_id'=>$product_id,'model'=>$model,'name'=>$name,
            'categories'=>implode(' | ', array_unique($category_names)),'category_ids'=>array_values($category_ids),'keyword'=>$keyword,
            'price'=>($price === '' ? '' : number_format((float)$price, 2, ',', '.')),'status'=>$status ? '1':'0',
            'errors'=>$errors,'warnings'=>$warnings,'source'=>$row
        );
    }

    private function buildProductData($row, $product_id = 0) {
        $this->load->model('catalog/product');
        $lang = $this->getLanguageId();
        $existing = $product_id ? $this->model_catalog_product->getProduct($product_id) : array();
        $descs = $product_id ? $this->model_catalog_product->getProductDescriptions($product_id) : array();
        $current_desc = isset($descs[$lang]) ? $descs[$lang] : array();

        $name = $this->field($row, array('urun_adi','ürün_adı','ürün adı','name','name(tr-tr)'));
        $model = $this->field($row, array('model','model_kodu','model kodu'));
        $keyword = $this->cleanKeyword($this->field($row, array('seo_url','seo_keyword','seo url')));
        if ($keyword === '') $keyword = $product_id ? $this->getProductKeyword($product_id) : $this->slugify($name);

        $desc = $this->field($row, array('aciklama_html','açıklama_html','description','description(tr-tr)'));
        if ($desc === '' && $product_id && isset($current_desc['description'])) $desc = $current_desc['description'];
        $meta_title = $this->field($row, array('meta_title','meta_title(tr-tr)'));
        if ($meta_title === '') $meta_title = ($product_id && !empty($current_desc['meta_title'])) ? $current_desc['meta_title'] : $name;
        $meta_description = $this->field($row, array('meta_description','meta_description(tr-tr)'));
        if ($meta_description === '' && $product_id && isset($current_desc['meta_description'])) $meta_description = $current_desc['meta_description'];
        $meta_keyword = $this->field($row, array('meta_keywords','meta_keywords(tr-tr)','meta_keyword'));
        if ($meta_keyword === '' && $product_id && isset($current_desc['meta_keyword'])) $meta_keyword = $current_desc['meta_keyword'];
        $tag = $this->field($row, array('etiketler','tags','tags(tr-tr)','tag'));
        if ($tag === '' && $product_id && isset($current_desc['tag'])) $tag = $current_desc['tag'];

        $categories_raw = $this->field($row, array('kategori','category','categories'));
        $category_ids = array();
        foreach (preg_split('/\s*[|;]\s*/u', $categories_raw, -1, PREG_SPLIT_NO_EMPTY) as $part) {
            $cat = $this->findCategory(trim($part));
            if ($cat) foreach ($this->getCategoryPathIds($cat['category_id']) as $cid) $category_ids[$cid] = $cid;
        }

        $image_field = $this->field($row, array('ana_gorsel','ana_görsel','image','image_name'));
        $image = $image_field !== '' ? $this->normalizeImage($image_field) : ($product_id && isset($existing['image']) ? $existing['image'] : '');
        $additional_field = $this->field($row, array('ek_gorseller','ek_görseller','additional_images'));
        if ($additional_field !== '') {
            $product_images = array(); $so = 0;
            foreach ($this->parseImageList($additional_field) as $img) $product_images[] = array('image'=>$img,'sort_order'=>$so++);
        } else {
            $product_images = $product_id ? $this->model_catalog_product->getProductImages($product_id) : array();
        }

        $attributes_from_file = $this->extractAttributes($row);
        if (!empty($attributes_from_file)) {
            $product_attributes = array();
            foreach ($attributes_from_file as $attr_name => $text) {
                $attribute_id = $this->getOrCreateAttribute($attr_name, $lang);
                $product_attributes[] = array('attribute_id'=>$attribute_id,'product_attribute_description'=>array($lang=>array('text'=>$text)));
            }
        } else {
            $product_attributes = $product_id ? $this->model_catalog_product->getProductAttributes($product_id) : array();
        }

        $price_field = $this->field($row, array('fiyat','price'));
        $price = $price_field !== '' ? $this->parsePrice($price_field) : ($product_id && isset($existing['price']) ? (float)$existing['price'] : 0.0);
        if ($price === false) $price = 0.0;
        $quantity_field = $this->field($row, array('miktar','quantity'));
        $quantity = $quantity_field !== '' ? (int)$quantity_field : ($product_id && isset($existing['quantity']) ? (int)$existing['quantity'] : 100);
        $sort_field = $this->field($row, array('siralama','sıralama','sort_order'));
        $sort_order = $sort_field !== '' ? (int)$sort_field : ($product_id && isset($existing['sort_order']) ? (int)$existing['sort_order'] : 0);
        $status_field = $this->field($row, array('durum','status'));
        $status = $status_field !== '' ? $this->parseStatus($status_field, 1) : ($product_id && isset($existing['status']) ? (int)$existing['status'] : 1);

        $data = array(
            'model'=>$model,
            'sku'=>$product_id && isset($existing['sku']) ? $existing['sku'] : '',
            'upc'=>$product_id && isset($existing['upc']) ? $existing['upc'] : '',
            'ean'=>$product_id && isset($existing['ean']) ? $existing['ean'] : '',
            'jan'=>$product_id && isset($existing['jan']) ? $existing['jan'] : '',
            'isbn'=>$product_id && isset($existing['isbn']) ? $existing['isbn'] : '',
            'mpn'=>$product_id && isset($existing['mpn']) ? $existing['mpn'] : '',
            'location'=>$product_id && isset($existing['location']) ? $existing['location'] : '',
            'quantity'=>$quantity,
            'minimum'=>$product_id && isset($existing['minimum']) ? (int)$existing['minimum'] : 1,
            'subtract'=>$product_id && isset($existing['subtract']) ? (int)$existing['subtract'] : 0,
            'stock_status_id'=>$product_id && isset($existing['stock_status_id']) ? (int)$existing['stock_status_id'] : (int)$this->config->get('config_stock_status_id'),
            'date_available'=>$product_id && !empty($existing['date_available']) ? $existing['date_available'] : date('Y-m-d'),
            'manufacturer_id'=>$product_id && isset($existing['manufacturer_id']) ? (int)$existing['manufacturer_id'] : 0,
            'shipping'=>$product_id && isset($existing['shipping']) ? (int)$existing['shipping'] : 1,
            'price'=>(float)$price,
            'points'=>$product_id && isset($existing['points']) ? (int)$existing['points'] : 0,
            'weight'=>$product_id && isset($existing['weight']) ? (float)$existing['weight'] : 0,
            'weight_class_id'=>$product_id && isset($existing['weight_class_id']) ? (int)$existing['weight_class_id'] : (int)$this->config->get('config_weight_class_id'),
            'length'=>$product_id && isset($existing['length']) ? (float)$existing['length'] : 0,
            'width'=>$product_id && isset($existing['width']) ? (float)$existing['width'] : 0,
            'height'=>$product_id && isset($existing['height']) ? (float)$existing['height'] : 0,
            'length_class_id'=>$product_id && isset($existing['length_class_id']) ? (int)$existing['length_class_id'] : (int)$this->config->get('config_length_class_id'),
            'status'=>(int)$status,
            'tax_class_id'=>$product_id && isset($existing['tax_class_id']) ? (int)$existing['tax_class_id'] : 0,
            'sort_order'=>$sort_order,
            'image'=>$image,
            'keyword'=>$keyword,
            'product_description'=>array($lang=>array('name'=>$name,'description'=>$desc,'tag'=>$tag,'meta_title'=>$meta_title,'meta_description'=>$meta_description,'meta_keyword'=>$meta_keyword)),
            'product_store'=>$product_id ? $this->model_catalog_product->getProductStores($product_id) : array(0),
            'product_attribute'=>$product_attributes,
            'product_image'=>$product_images,
            'product_category'=>array_values($category_ids),
            'product_filter'=>$product_id ? $this->model_catalog_product->getProductFilters($product_id) : array(),
            'product_option'=>$product_id ? $this->model_catalog_product->getProductOptions($product_id) : array(),
            'product_discount'=>$product_id ? $this->model_catalog_product->getProductDiscounts($product_id) : array(),
            'product_special'=>$product_id ? $this->model_catalog_product->getProductSpecials($product_id) : array(),
            'product_download'=>$product_id ? $this->model_catalog_product->getProductDownloads($product_id) : array(),
            'product_related'=>$product_id ? $this->model_catalog_product->getProductRelated($product_id) : array(),
            'product_reward'=>$product_id ? $this->model_catalog_product->getProductRewards($product_id) : array(),
            'product_layout'=>$product_id ? $this->model_catalog_product->getProductLayouts($product_id) : array(),
            'product_recurring'=>$product_id ? $this->model_catalog_product->getRecurrings($product_id) : array()
        );
        if (empty($data['stock_status_id'])) $data['stock_status_id'] = 6;
        if (empty($data['weight_class_id'])) $data['weight_class_id'] = 1;
        if (empty($data['length_class_id'])) $data['length_class_id'] = 1;
        if (empty($data['product_store'])) $data['product_store'] = array(0);
        return $data;
    }

    private function parseCsv($path) {
        $fh = fopen($path, 'rb');
        if (!$fh) throw new Exception('CSV açılamadı.');
        $first = fgets($fh);
        if ($first === false) { fclose($fh); throw new Exception('CSV boş.'); }
        $first = preg_replace('/^\xEF\xBB\xBF/', '', $first);
        $delims = array(';'=>substr_count($first,';'), ','=>substr_count($first,','), "\t"=>substr_count($first,"\t"));
        arsort($delims); $delimiter = key($delims);
        rewind($fh);
        $rows = array();
        while (($data = fgetcsv($fh, 0, $delimiter)) !== false) {
            if (isset($data[0])) $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
            $rows[] = array_map(array($this,'ensureUtf8'), $data);
        }
        fclose($fh);
        return $rows;
    }

    private function parseXlsx($path) {
        if (!class_exists('ZipArchive')) {
            throw new Exception('Sunucuda PHP ZipArchive kapalı. XLSX yerine CSV kullanın.');
        }

        if (!function_exists('simplexml_load_string')) {
            throw new Exception('Sunucuda PHP SimpleXML kapalı. XLSX yerine CSV kullanın.');
        }

        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new Exception('XLSX ZIP yapısı açılamadı.');
        }

        /*
         * Namespace bağımsız XLSX okuyucu.
         *
         * Bazı Excel üreticileri <worksheet>, bazıları <x:worksheet> kullanır.
         * SimpleXML children() yaklaşımı alt düğümlerde inherited namespace'i
         * her zaman güvenilir biçimde döndürmediği için bazı geçerli dosyalar
         * boş satırlar olarak okunabiliyordu.
         *
         * local-name() XPath kullanarak prefix/default namespace farkını tamamen
         * ortadan kaldırıyoruz.
         */

        $shared = array();
        $shared_xml_raw = $zip->getFromName('xl/sharedStrings.xml');

        if ($shared_xml_raw !== false && trim($shared_xml_raw) !== '') {
            libxml_use_internal_errors(true);
            $shared_xml = simplexml_load_string($shared_xml_raw);

            if ($shared_xml !== false) {
                $items = $shared_xml->xpath('/*[local-name()="sst"]/*[local-name()="si"]');

                if ($items !== false) {
                    foreach ($items as $si) {
                        $value = '';
                        $texts = $si->xpath('.//*[local-name()="t"]');

                        if ($texts !== false) {
                            foreach ($texts as $t) {
                                $value .= (string)$t;
                            }
                        }

                        $shared[] = $value;
                    }
                }
            }

            libxml_clear_errors();
        }

        $sheet_xml_raw = $zip->getFromName('xl/worksheets/sheet1.xml');

        if ($sheet_xml_raw === false) {
            $zip->close();
            throw new Exception('XLSX sheet1 bulunamadı.');
        }

        libxml_use_internal_errors(true);
        $sheet_xml = simplexml_load_string($sheet_xml_raw);

        if ($sheet_xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            $zip->close();

            $detail = '';
            if (!empty($errors)) {
                $detail = trim($errors[0]->message);
            }

            throw new Exception('XLSX XML çözümlenemedi' . ($detail !== '' ? ': ' . $detail : '.'));
        }

        libxml_clear_errors();

        $row_nodes = $sheet_xml->xpath('/*[local-name()="worksheet"]/*[local-name()="sheetData"]/*[local-name()="row"]');

        if ($row_nodes === false || empty($row_nodes)) {
            $zip->close();
            throw new Exception('XLSX çalışma sayfasında satır bulunamadı.');
        }

        $matrix = array();

        foreach ($row_nodes as $row) {
            $cell_nodes = $row->xpath('./*[local-name()="c"]');
            $out = array();
            $max = -1;

            if ($cell_nodes !== false) {
                foreach ($cell_nodes as $c) {
                    $attrs = $c->attributes();
                    $ref = isset($attrs['r']) ? (string)$attrs['r'] : '';

                    if (!preg_match('/([A-Z]+)/', $ref, $match)) {
                        continue;
                    }

                    $idx = $this->columnIndex($match[1]);
                    $type = isset($attrs['t']) ? (string)$attrs['t'] : '';
                    $value = '';

                    if ($type === 's') {
                        $v_nodes = $c->xpath('./*[local-name()="v"]');
                        $n = ($v_nodes !== false && isset($v_nodes[0])) ? (int)((string)$v_nodes[0]) : -1;
                        $value = isset($shared[$n]) ? $shared[$n] : '';
                    } elseif ($type === 'inlineStr') {
                        $t_nodes = $c->xpath('./*[local-name()="is"]//*[local-name()="t"]');

                        if ($t_nodes !== false) {
                            foreach ($t_nodes as $t) {
                                $value .= (string)$t;
                            }
                        }
                    } else {
                        // t="str", numeric, boolean ve diğer basit hücre tipleri.
                        $v_nodes = $c->xpath('./*[local-name()="v"]');

                        if ($v_nodes !== false && isset($v_nodes[0])) {
                            $value = (string)$v_nodes[0];
                        }
                    }

                    $out[$idx] = $value;

                    if ($idx > $max) {
                        $max = $idx;
                    }
                }
            }

            $line = array();

            for ($i = 0; $i <= $max; $i++) {
                $line[] = isset($out[$i]) ? $out[$i] : '';
            }

            $matrix[] = $line;
        }

        $zip->close();

        if (empty($matrix)) {
            throw new Exception('XLSX içinde okunabilir satır bulunamadı.');
        }

        return $matrix;
    }

    private function matrixToRows($matrix) {
        if (empty($matrix)) {
            throw new Exception('Dosya boş.');
        }

        $headers = array_shift($matrix);
        $keys = array();
        $non_empty_headers = 0;

        foreach ($headers as $h) {
            $key = trim($this->ensureUtf8((string)$h));
            $keys[] = $key;

            if ($key !== '') {
                $non_empty_headers++;
            }
        }

        if ($non_empty_headers === 0) {
            throw new Exception('Dosya başlık satırı okunamadı. XLSX/CSV yapısını kontrol edin.');
        }

        $rows = array();
        $non_empty_rows = 0;

        foreach ($matrix as $line) {
            $row = array();

            foreach ($keys as $i => $key) {
                if ($key !== '') {
                    $row[$key] = isset($line[$i]) ? $this->ensureUtf8((string)$line[$i]) : '';
                }
            }

            if (!$this->rowIsEmpty($row)) {
                $non_empty_rows++;
            }

            $rows[] = $row;
        }

        if ($non_empty_rows === 0) {
            throw new Exception('Dosyada başlık bulundu ancak ürün satırları okunamadı.');
        }

        return $rows;
    }

    private function findProductByModel($model) {
        $q = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE model='" . $this->db->escape($model) . "' ORDER BY product_id ASC LIMIT 1");
        return $q->num_rows ? (int)$q->row['product_id'] : 0;
    }
    private function findProductByKeyword($keyword) {
        $q = $this->db->query("SELECT query FROM " . DB_PREFIX . "url_alias WHERE keyword='" . $this->db->escape($keyword) . "' AND query LIKE 'product_id=%' LIMIT 1");
        if (!$q->num_rows) return 0;
        return (int)substr($q->row['query'], 11);
    }
    private function getProductKeyword($product_id) {
        $q = $this->db->query("SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query='product_id=" . (int)$product_id . "' LIMIT 1");
        return $q->num_rows ? $q->row['keyword'] : '';
    }
    private function getAliasConflict($keyword, $product_id) {
        $q = $this->db->query("SELECT query FROM " . DB_PREFIX . "url_alias WHERE keyword='" . $this->db->escape($keyword) . "' LIMIT 1");
        if (!$q->num_rows) return '';
        if ($product_id && $q->row['query'] === 'product_id=' . (int)$product_id) return '';
        return $q->row['query'];
    }
    private function findCategory($needle) {
        $lang = $this->getLanguageId();
        $q = $this->db->query("SELECT c.category_id, cd.name FROM " . DB_PREFIX . "url_alias ua JOIN " . DB_PREFIX . "category c ON ua.query=CONCAT('category_id=',c.category_id) JOIN " . DB_PREFIX . "category_description cd ON cd.category_id=c.category_id AND cd.language_id='" . (int)$lang . "' WHERE ua.keyword='" . $this->db->escape($this->cleanKeyword($needle)) . "' AND c.status=1 LIMIT 1");
        if ($q->num_rows) return $q->row;
        $q = $this->db->query("SELECT c.category_id, cd.name FROM " . DB_PREFIX . "category c JOIN " . DB_PREFIX . "category_description cd ON cd.category_id=c.category_id AND cd.language_id='" . (int)$lang . "' WHERE cd.name='" . $this->db->escape($needle) . "' AND c.status=1 ORDER BY c.category_id ASC LIMIT 1");
        return $q->num_rows ? $q->row : false;
    }
    private function getCategoryPathIds($category_id) {
        $ids = array();
        $q = $this->db->query("SELECT path_id FROM " . DB_PREFIX . "category_path WHERE category_id='" . (int)$category_id . "' ORDER BY level ASC");
        if ($q->num_rows) foreach ($q->rows as $r) $ids[] = (int)$r['path_id'];
        if (empty($ids)) $ids[] = (int)$category_id;
        return $ids;
    }

    private function getLanguageId() {
        if ($this->language_id) return $this->language_id;
        $id = (int)$this->config->get('config_language_id');
        if ($id) { $this->language_id = $id; return $id; }
        $code = $this->config->get('config_language'); if (!$code) $code='tr-tr';
        $q = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code='" . $this->db->escape($code) . "' LIMIT 1");
        $this->language_id = $q->num_rows ? (int)$q->row['language_id'] : 2;
        return $this->language_id;
    }

    private function getOrCreateAttribute($name, $lang) {
        $q = $this->db->query("SELECT a.attribute_id FROM " . DB_PREFIX . "attribute a JOIN " . DB_PREFIX . "attribute_description ad ON a.attribute_id=ad.attribute_id WHERE ad.language_id='" . (int)$lang . "' AND ad.name='" . $this->db->escape($name) . "' LIMIT 1");
        if ($q->num_rows) return (int)$q->row['attribute_id'];
        $group_id = $this->getOrCreateAttributeGroup('Ürün Özellikleri', $lang);
        $this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET attribute_group_id='" . (int)$group_id . "', sort_order=0");
        $id = $this->db->getLastId();
        $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id='" . (int)$id . "', language_id='" . (int)$lang . "', name='" . $this->db->escape($name) . "'");
        return (int)$id;
    }
    private function getOrCreateAttributeGroup($name, $lang) {
        $q = $this->db->query("SELECT ag.attribute_group_id FROM " . DB_PREFIX . "attribute_group ag JOIN " . DB_PREFIX . "attribute_group_description agd ON ag.attribute_group_id=agd.attribute_group_id WHERE agd.language_id='" . (int)$lang . "' AND agd.name='" . $this->db->escape($name) . "' LIMIT 1");
        if ($q->num_rows) return (int)$q->row['attribute_group_id'];
        $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group SET sort_order=0");
        $id = $this->db->getLastId();
        $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description SET attribute_group_id='" . (int)$id . "', language_id='" . (int)$lang . "', name='" . $this->db->escape($name) . "'");
        return (int)$id;
    }

    private function extractAttributes($row) {
        $attrs = array();
        foreach ($row as $key=>$value) {
            $v = trim((string)$value); if ($v==='') continue;
            $k = trim((string)$key); $lower = $this->lower($k);
            if (strpos($lower,'ozellik:')===0 || strpos($lower,'özellik:')===0 || strpos($lower,'attribute:')===0) {
                $pos = strpos($k, ':'); $name = $pos!==false ? trim(substr($k,$pos+1)) : '';
                if ($name!=='') $attrs[$name]=$v;
            }
        }
        return $attrs;
    }

    private function field($row, $aliases) {
        $map = array(); foreach ($row as $k=>$v) $map[$this->normalizeHeader($k)] = trim((string)$v);
        foreach ($aliases as $a) { $n=$this->normalizeHeader($a); if (array_key_exists($n,$map)) return $map[$n]; }
        return '';
    }
    private function normalizeHeader($s) {
        $s = $this->lower(trim((string)$s));
        $s = str_replace(array(' ','-'), '_', $s);
        return $s;
    }
    private function rowIsEmpty($row) { foreach ($row as $v) if (trim((string)$v)!=='') return false; return true; }
    public function ensureUtf8($s) {
        if ($s === null) return '';
        if (function_exists('mb_check_encoding') && mb_check_encoding($s,'UTF-8')) return $s;
        if (function_exists('mb_convert_encoding')) return mb_convert_encoding($s,'UTF-8','Windows-1254,ISO-8859-9,ISO-8859-1');
        return $s;
    }
    private function lower($s) { return function_exists('mb_strtolower') ? mb_strtolower((string)$s,'UTF-8') : strtolower((string)$s); }
    private function normalizeImage($s) { return ltrim(str_replace('\\','/',trim((string)$s)),'/'); }
    private function parseImageList($s) {
        $out=array(); foreach (preg_split('/\s*[|;]\s*/u',trim((string)$s),-1,PREG_SPLIT_NO_EMPTY) as $x) { $x=$this->normalizeImage($x); if ($x!=='') $out[]=$x; } return array_values(array_unique($out));
    }
    private function parseStatus($s,$default=1) {
        $v=$this->lower(trim((string)$s)); if ($v==='') return (int)$default;
        if (in_array($v,array('1','true','yes','evet','aktif','active'),true)) return 1;
        if (in_array($v,array('0','false','no','hayir','hayır','pasif','inactive'),true)) return 0;
        return (int)$default;
    }
    private function parsePrice($s) {
        $v=trim((string)$s); if ($v==='') return '';
        $v=preg_replace('/[^0-9,\.\-]/u','',$v); if ($v===''||$v==='-') return false;
        $comma=strrpos($v,','); $dot=strrpos($v,'.');
        if ($comma!==false && $dot!==false) {
            if ($comma>$dot) { $v=str_replace('.','',$v); $v=str_replace(',','.',$v); }
            else { $v=str_replace(',','',$v); }
        } elseif ($comma!==false) {
            $after=strlen($v)-$comma-1; if ($after===3) $v=str_replace(',','',$v); else $v=str_replace(',','.',$v);
        } elseif ($dot!==false) {
            $after=strlen($v)-$dot-1; if ($after===3) $v=str_replace('.','',$v);
        }
        return is_numeric($v) ? (float)$v : false;
    }
    private function cleanKeyword($s) { return trim(ltrim((string)$s,'/')); }
    private function slugify($s) {
        $s=$this->lower(strip_tags(html_entity_decode((string)$s,ENT_QUOTES,'UTF-8')));
        $map=array('ç'=>'c','ğ'=>'g','ı'=>'i','ö'=>'o','ş'=>'s','ü'=>'u','â'=>'a','î'=>'i','û'=>'u','²'=>'2');
        $s=strtr($s,$map); $s=preg_replace('/[^a-z0-9]+/','-',$s); return trim($s,'-');
    }
    private function columnIndex($letters) {
        $n=0; for($i=0;$i<strlen($letters);$i++) $n=$n*26+(ord($letters[$i])-64); return $n-1;
    }
}
