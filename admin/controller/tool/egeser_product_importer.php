<?php
class ControllerToolEgeserProductImporter extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('tool/egeser_product_importer');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tool/egeser_product_importer');

        $data = array();
        $data['error_warning'] = '';
        $data['success'] = '';
        $data['preview'] = array();
        $data['summary'] = array();
        $data['import_key'] = '';
        $data['allow_update'] = !empty($this->request->post['allow_update']);

        if (isset($this->session->data['egeser_import_success'])) {
            $data['success'] = $this->session->data['egeser_import_success'];
            unset($this->session->data['egeser_import_success']);
        }

        if (isset($this->request->get['clear'])) {
            $this->clearCachedImport();
            $this->response->redirect($this->url->link('tool/egeser_product_importer', 'token=' . $this->session->data['token'], true));
            return;
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $mode = isset($this->request->post['mode']) ? $this->request->post['mode'] : 'preview';

            if (!$this->validatePermission()) {
                $data['error_warning'] = $this->error['warning'];
            } elseif ($mode === 'preview') {
                $result = $this->handlePreview();
                if (!empty($result['error'])) {
                    $data['error_warning'] = $result['error'];
                } else {
                    $data['preview'] = $result['preview']['rows'];
                    $data['summary'] = $result['preview']['summary'];
                    $data['import_key'] = $result['key'];
                }
            } elseif ($mode === 'apply') {
                if (empty($this->request->post['confirm_apply'])) {
                    $data['error_warning'] = $this->language->get('error_confirm');
                } else {
                    $key = isset($this->request->post['import_key']) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $this->request->post['import_key']) : '';
                    $cached = $this->getCachedImport($key);
                    if (!$cached || !is_file($cached['path'])) {
                        $data['error_warning'] = $this->language->get('error_preview_expired');
                    } else {
                        try {
                            $rows = $this->model_tool_egeser_product_importer->parseFile($cached['path'], $cached['name']);
                            $apply = $this->model_tool_egeser_product_importer->applyRows($rows, !empty($this->request->post['allow_update']));
                            if (!empty($apply['error'])) {
                                $data['error_warning'] = $apply['error'];
                                $data['preview'] = $apply['preview']['rows'];
                                $data['summary'] = $apply['preview']['summary'];
                                $data['import_key'] = $key;
                            } else {
                                $this->clearCachedImport();
                                $this->session->data['egeser_import_success'] = $this->language->get('text_success_apply') . ' Yeni: ' . (int)$apply['created'] . ', Güncellenen: ' . (int)$apply['updated'] . ', Atlanan: ' . (int)$apply['skipped'] . '.';
                                $this->response->redirect($this->url->link('tool/egeser_product_importer', 'token=' . $this->session->data['token'], true));
                                return;
                            }
                        } catch (Exception $e) {
                            $data['error_warning'] = 'Aktarım sırasında hata: ' . $e->getMessage();
                            $this->model_tool_egeser_product_importer->writeLog('APPLY ERROR: ' . $e->getMessage());
                        }
                    }
                }
            }
        }

        $data['heading_title'] = $this->language->get('heading_title');
        foreach (array(
            'text_preview','text_no_preview','text_template_help','text_apply_warning','text_create','text_update','text_skip','text_error','text_active','text_passive','text_summary','text_rows','text_creates','text_updates','text_skips','text_errors','text_warnings',
            'button_preview','button_apply','button_template','button_cancel_preview','entry_file','entry_allow_update','help_allow_update',
            'column_row','column_action','column_model','column_name','column_categories','column_keyword','column_price','column_status','column_notes'
        ) as $key) {
            $data[$key] = $this->language->get($key);
        }

        $data['xlsx_available'] = class_exists('ZipArchive');
        $data['token'] = $this->session->data['token'];
        $data['action'] = $this->url->link('tool/egeser_product_importer', 'token=' . $this->session->data['token'], true);
        $data['template_url'] = $this->url->link('tool/egeser_product_importer/template', 'token=' . $this->session->data['token'], true);
        $data['clear_url'] = $this->url->link('tool/egeser_product_importer', 'token=' . $this->session->data['token'] . '&clear=1', true);

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true));
        $data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'), 'href' => $data['action']);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('tool/egeser_product_importer.tpl', $data));
    }

    public function template() {
        $this->load->language('tool/egeser_product_importer');
        if (!$this->validatePermission()) {
            $this->response->addHeader('HTTP/1.1 403 Forbidden');
            $this->response->setOutput('Forbidden');
            return;
        }

        $headers = array(
            'islem','model','urun_adi','kategori','seo_url','aciklama_html','meta_title','meta_description','etiketler','ana_gorsel','ek_gorseller','fiyat','durum','siralama','miktar',
            'ozellik:Toplam Alan','ozellik:Plan','ozellik:Dış Ölçü','ozellik:Veranda','ozellik:WC / Banyo','ozellik:Mutfak','ozellik:Zemin','ozellik:Çatı Sistemi','ozellik:Isı Yalıtımı','ozellik:PVC Doğrama','ozellik:Dış Cephe'
        );
        $example = array(
            'AUTO','NUVARA-66','66 m² Premium Modül Ev','Prefabrik Yapılar|Tek Katlı Prefabrik Evler|Tüm Prefabrik Ev Modelleri','66-m2-premium-modul-ev',
            '<p>Ürün açıklaması buraya.</p>','66 m² Premium Modül Ev | Egeser Prefabrik','66 m² 3+1 prefabrik/modül ev. Teknik özellikler ve teklif için Egeser Prefabrik ile iletişime geçin.',
            '66 m², 3+1, prefabrik ev','catalog/urunler/nuvara-66/ana.jpg','catalog/urunler/nuvara-66/2.jpg|catalog/urunler/nuvara-66/3.jpg','0','1','10','100',
            '66 m²','3+1','', '', '', '', '', '', '', '', '', ''
        );

        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $headers, ';');
        fputcsv($out, $example, ';');
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        $this->response->addHeader('Content-Type: text/csv; charset=UTF-8');
        $this->response->addHeader('Content-Disposition: attachment; filename="Egeser_Urun_Import_Sablonu.csv"');
        $this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate');
        $this->response->setOutput($csv);
    }

    private function handlePreview() {
        // OpenCart 2.3 Request::clean() $_FILES içindeki sayısal değerleri string'e çevirir.
        // Bu nedenle strict karşılaştırma ('0' !== 0) geçerli yüklemeyi hatalı sayıyordu.
        if (!isset($this->request->files['import_file'])) {
            return array('error' => 'Dosya sunucuya ulaşmadı. Lütfen dosyayı yeniden seçip deneyin.');
        }

        $upload_error = isset($this->request->files['import_file']['error'])
            ? (int)$this->request->files['import_file']['error']
            : UPLOAD_ERR_NO_FILE;

        if ($upload_error !== UPLOAD_ERR_OK) {
            $upload_messages = array(
                UPLOAD_ERR_INI_SIZE   => 'Dosya PHP upload_max_filesize sınırını aşıyor.',
                UPLOAD_ERR_FORM_SIZE  => 'Dosya form yükleme sınırını aşıyor.',
                UPLOAD_ERR_PARTIAL    => 'Dosya yalnızca kısmen yüklendi.',
                UPLOAD_ERR_NO_FILE    => 'Dosya seçilmedi veya sunucuya ulaşmadı.',
                UPLOAD_ERR_NO_TMP_DIR => 'Sunucuda geçici yükleme klasörü bulunamadı.',
                UPLOAD_ERR_CANT_WRITE => 'Sunucu geçici dosyayı diske yazamadı.',
                UPLOAD_ERR_EXTENSION  => 'Bir PHP eklentisi dosya yüklemesini durdurdu.'
            );
            $detail = isset($upload_messages[$upload_error]) ? $upload_messages[$upload_error] : 'Bilinmeyen yükleme hatası.';
            return array('error' => 'Dosya yüklenemedi (kod ' . $upload_error . '): ' . $detail);
        }

        $name = basename($this->request->files['import_file']['name']);
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, array('xlsx','csv'))) {
            return array('error' => $this->language->get('error_file_type'));
        }

        $key = 'egimp_' . substr(sha1($this->session->data['token'] . microtime(true) . mt_rand()), 0, 20);
        $cache_dir = defined('DIR_CACHE') ? DIR_CACHE : (DIR_SYSTEM . 'storage/cache/');
        $path = rtrim($cache_dir, '/\\') . DIRECTORY_SEPARATOR . $key . '.' . $ext;

        if (!is_dir($cache_dir)) {
            return array('error' => 'Import önbellek klasörü bulunamadı: ' . $cache_dir);
        }

        if (!is_writable($cache_dir)) {
            return array('error' => 'Import önbellek klasörüne yazma izni yok: ' . $cache_dir);
        }

        if (!move_uploaded_file($this->request->files['import_file']['tmp_name'], $path)) {
            return array('error' => 'Yüklenen dosya geçici klasörden import önbelleğine taşınamadı.');
        }

        try {
            $rows = $this->model_tool_egeser_product_importer->parseFile($path, $name);
            $preview = $this->model_tool_egeser_product_importer->analyzeRows($rows, !empty($this->request->post['allow_update']));
        } catch (Exception $e) {
            @unlink($path);
            return array('error' => 'Dosya okunamadı: ' . $e->getMessage());
        }

        $this->clearCachedImport();
        $this->session->data['egeser_import_cache'] = array('key' => $key, 'path' => $path, 'name' => $name, 'created' => time());
        return array('key' => $key, 'preview' => $preview);
    }

    private function getCachedImport($key) {
        if (empty($this->session->data['egeser_import_cache'])) return false;
        $c = $this->session->data['egeser_import_cache'];
        if (empty($c['key']) || $c['key'] !== $key) return false;
        if (empty($c['created']) || (time() - (int)$c['created']) > 7200) {
            $this->clearCachedImport();
            return false;
        }
        return $c;
    }

    private function clearCachedImport() {
        if (!empty($this->session->data['egeser_import_cache']['path'])) {
            $path = $this->session->data['egeser_import_cache']['path'];
            if (is_file($path)) @unlink($path);
        }
        unset($this->session->data['egeser_import_cache']);
    }

    protected function validatePermission() {
        if (!$this->user->hasPermission('modify', 'catalog/product')) {
            $this->error['warning'] = $this->language->get('error_permission');
            return false;
        }
        return true;
    }
}
