<?php
/* EGESER - Her ürünün görsellerini (hangi klasörde dağınık duruyor
   olursa olsun) "catalog/urunler/<ürün-adı>-<id>/" altında tek bir
   klasörde toplayan ve veritabanındaki ilgili görsel kayıtlarını aynı
   anda güncelleyen araç. Önce sadece okur (scan), onay sonrası
   ürün başına uygular (apply) ve her uygulamayı geri alınabilir
   şekilde loglar. Birden fazla ürünün paylaştığı bir klasörden
   (örn. ortak bir "tmp" yer tutucu) gelen görsellere hiç dokunmaz. */
class ModelCatalogEgeserFolderOrganizer extends Model {
    private $base_relative = 'catalog/urunler/';

    private function getImageDir() {
        return rtrim(DIR_IMAGE, '/') . '/';
    }

    private function getLogDir() {
        /* admin/config.php'de DIR_CATALOG yok; DIR_APPLICATION'dan
           ("..../admin/") site köküne çıkıp catalog/ tarafındaki
           egeser veri klasörünü kullanıyoruz (zaten yazılabilir). */
        return rtrim(dirname(rtrim(DIR_APPLICATION, '/')), '/') . '/catalog/view/theme/egeser/data/folder-rename-log/';
    }

    private function slugify($s) {
        $s = function_exists('mb_strtolower') ? mb_strtolower(strip_tags((string)$s), 'UTF-8') : strtolower(strip_tags((string)$s));
        $map = array('ç' => 'c', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u', 'â' => 'a', 'î' => 'i', 'û' => 'u', '²' => '2');
        $s = strtr($s, $map);
        $s = preg_replace('/[^a-z0-9]+/', '-', $s);
        return trim($s, '-');
    }

    /* Veritabanındaki TÜM ürün görsellerini (herhangi bir klasörde
       olursa olsun) okuyup ürün başına grupluyor. Bir klasör birden
       fazla ürün tarafından kullanılıyorsa (paylaşılan/placeholder
       klasör), o klasördeki görseller "güvensiz" sayılır ve hiçbir
       ürün için taşınmaya aday gösterilmez. */
    private function loadProductImageMap() {
        $rows = array();

        $q1 = $this->db->query("SELECT product_id, image FROM " . DB_PREFIX . "product WHERE image IS NOT NULL AND image <> '' AND image <> 'no_image.png'");
        foreach ($q1->rows as $r) {
            $rows[] = array('source' => 'product', 'pk' => (int)$r['product_id'], 'product_id' => (int)$r['product_id'], 'image' => $r['image']);
        }

        $q2 = $this->db->query("SELECT product_image_id, product_id, image FROM " . DB_PREFIX . "product_image WHERE image IS NOT NULL AND image <> ''");
        foreach ($q2->rows as $r) {
            $rows[] = array('source' => 'product_image', 'pk' => (int)$r['product_image_id'], 'product_id' => (int)$r['product_id'], 'image' => $r['image']);
        }

        $folder_products = array(); // folder => [product_id => true]
        foreach ($rows as $r) {
            $folder = dirname($r['image']);
            if (!isset($folder_products[$folder])) { $folder_products[$folder] = array(); }
            $folder_products[$folder][$r['product_id']] = true;
        }

        $by_product = array();
        foreach ($rows as $r) {
            $folder = dirname($r['image']);
            $shared = count($folder_products[$folder]) > 1;
            $by_product[$r['product_id']][] = array(
                'source' => $r['source'],
                'pk'     => $r['pk'],
                'image'  => $r['image'],
                'folder' => $folder,
                'shared' => $shared
            );
        }

        return $by_product;
    }

    /* Salt okunur keşif: her ürünün görsellerini nerede bulunduğuna
       göre sınıflandırır. Hiçbir şeyi değiştirmez. */
    public function scan() {
        $by_product = $this->loadProductImageMap();

        $result = array('items' => array());

        if (!$by_product) { return $result; }

        $ids = implode(',', array_map('intval', array_keys($by_product)));
        $names = array();
        $qn = $this->db->query("SELECT product_id, name FROM " . DB_PREFIX . "product_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND product_id IN (" . $ids . ")");
        foreach ($qn->rows as $r) { $names[(int)$r['product_id']] = $r['name']; }

        $used_slugs = array();

        // Tutarlı sırayla göster: ürün adına göre.
        uksort($by_product, function($a, $b) use ($names) {
            $na = isset($names[$a]) ? $names[$a] : '';
            $nb = isset($names[$b]) ? $names[$b] : '';
            return strcmp($na, $nb);
        });

        foreach ($by_product as $product_id => $images) {
            $name = isset($names[$product_id]) ? $names[$product_id] : ('urun-' . $product_id);

            $movable = array();
            $blocked_count = 0;
            $folders = array();

            foreach ($images as $img) {
                $folders[$img['folder']] = true;
                if ($img['shared']) { $blocked_count++; } else { $movable[] = $img; }
            }

            $slug = $this->slugify($name);
            if ($slug === '') { $slug = 'urun'; }
            $new_slug = $slug . '-' . $product_id;
            $new_folder = $this->base_relative . $new_slug;

            $item = array(
                'product_id'    => $product_id,
                'product_name'  => $name,
                'current_folders' => array_keys($folders),
                'image_count'   => count($images),
                'movable_count' => count($movable),
                'blocked_count' => $blocked_count,
                'new_slug'      => $new_slug,
                'status'        => '',
                'note'          => ''
            );

            if ($blocked_count > 0) {
                $item['status'] = 'blocked';
                $item['note'] = $blocked_count . ' görsel, başka ürün(ler)le paylaşılan bir klasörde (muhtemelen yer tutucu/placeholder) — önce bu ürüne gerçek görsel yüklenmeli.';
            } elseif (count($folders) === 1 && array_keys($folders) === array($new_folder)) {
                $item['status'] = 'already-ok';
                $item['note'] = 'Zaten düzenli.';
            } elseif (is_dir($this->getImageDir() . $new_folder) || isset($used_slugs[$new_slug])) {
                $item['status'] = 'conflict';
                $item['note'] = 'Hedef klasör adı zaten var: ' . $new_folder;
            } else {
                $item['status'] = 'ready';
                $used_slugs[$new_slug] = true;
            }

            $result['items'][] = $item;
        }

        return $result;
    }

    /* Bir ürünün tüm (paylaşılmayan) görsellerini tek bir hedef
       klasöre taşır ve ilgili DB satırlarını tek tek günceller.
       Herhangi bir adım başarısız olursa, o ana kadar taşınan her
       şeyi eski haline geri döndürür (telafi edici işlem). */
    public function applyOne($product_id) {
        $fresh = $this->scan();
        $plan = null;
        foreach ($fresh['items'] as $item) {
            if ((int)$item['product_id'] === (int)$product_id) { $plan = $item; break; }
        }

        if (!$plan || $plan['status'] !== 'ready') {
            return array('success' => false, 'error' => 'Bu ürün artık uygun durumda değil (sayfayı yenileyip tekrar deneyin).');
        }

        $by_product = $this->loadProductImageMap();
        $images = isset($by_product[(int)$product_id]) ? $by_product[(int)$product_id] : array();
        $movable = array();
        foreach ($images as $img) { if (!$img['shared']) { $movable[] = $img; } }

        if (!$movable) {
            return array('success' => false, 'error' => 'Taşınacak görsel bulunamadı.');
        }

        $image_dir = $this->getImageDir();
        $new_rel_dir = $this->base_relative . $plan['new_slug'];
        $new_abs_dir = $image_dir . $new_rel_dir;

        if (is_dir($new_abs_dir) || file_exists($new_abs_dir)) {
            return array('success' => false, 'error' => 'Hedef klasör zaten var: ' . $new_rel_dir);
        }
        if (!@mkdir($new_abs_dir, 0755, true)) {
            return array('success' => false, 'error' => 'Hedef klasör oluşturulamadı (sunucu yazma izni?): ' . $new_rel_dir);
        }

        $moved = array(); // her biri: source, pk, old_image, new_image
        $used_names = array();

        foreach ($movable as $img) {
            $old_abs = $image_dir . $img['image'];

            if (!is_file($old_abs)) {
                $this->rollbackMoves($moved, $image_dir, $new_abs_dir);
                return array('success' => false, 'error' => 'Kaynak dosya bulunamadı: ' . $img['image']);
            }

            $basename = basename($img['image']);
            $candidate = $basename;
            $n = 1;
            while (isset($used_names[strtolower($candidate)])) {
                $info = pathinfo($basename);
                $ext = isset($info['extension']) ? '.' . $info['extension'] : '';
                $candidate = $info['filename'] . '-' . $n . $ext;
                $n++;
            }
            $used_names[strtolower($candidate)] = true;

            $new_rel = $new_rel_dir . '/' . $candidate;
            $new_abs = $image_dir . $new_rel;

            if (!@rename($old_abs, $new_abs)) {
                $this->rollbackMoves($moved, $image_dir, $new_abs_dir);
                return array('success' => false, 'error' => 'Dosya taşınamadı: ' . $img['image']);
            }

            $moved[] = array('source' => $img['source'], 'pk' => $img['pk'], 'old_image' => $img['image'], 'new_image' => $new_rel);
        }

        try {
            foreach ($moved as $m) {
                $this->writeImagePath($m['source'], $m['pk'], $m['new_image']);
            }
        } catch (Exception $e) {
            // Telafi: önce DB'yi (her ihtimale karşı) eski değerlere döndür, sonra dosyaları geri taşı.
            foreach ($moved as $m) {
                try { $this->writeImagePath($m['source'], $m['pk'], $m['old_image']); } catch (Exception $e2) { /* en iyi gayret */ }
            }
            $this->rollbackMoves($moved, $image_dir, $new_abs_dir);
            return array('success' => false, 'error' => 'Veritabanı güncellenemedi, dosyalar geri taşındı: ' . $e->getMessage());
        }

        // En iyi gayret: artık boşalmış eski klasörleri temizle.
        $old_folders = array();
        foreach ($moved as $m) { $old_folders[dirname($image_dir . $m['old_image'])] = true; }
        foreach (array_keys($old_folders) as $dir) {
            if (is_dir($dir)) {
                $remaining = array_diff(scandir($dir), array('.', '..'));
                if (!$remaining) { @rmdir($dir); }
            }
        }

        return array(
            'success'      => true,
            'product_id'   => $product_id,
            'product_name' => $plan['product_name'],
            'new_slug'     => $plan['new_slug'],
            'moved'        => $moved
        );
    }

    /* Bir applyOne() sonucunu geri alır: dosyaları eski klasörlerine,
       DB satırlarını eski yollarına döndürür. */
    public function revertOne($entry) {
        $image_dir = $this->getImageDir();
        $moved = isset($entry['moved']) && is_array($entry['moved']) ? $entry['moved'] : array();

        if (!$moved) {
            return array('success' => false, 'error' => 'Geri alınacak kayıt bulunamadı.');
        }

        $errors = array();

        foreach ($moved as $m) {
            $old_abs = $image_dir . $m['old_image'];
            $new_abs = $image_dir . $m['new_image'];

            if (is_file($new_abs)) {
                @mkdir(dirname($old_abs), 0755, true);
                if (!@rename($new_abs, $old_abs)) {
                    $errors[] = 'Dosya geri taşınamadı: ' . $m['new_image'];
                    continue;
                }
            }

            try {
                $this->writeImagePath($m['source'], $m['pk'], $m['old_image']);
            } catch (Exception $e) {
                $errors[] = 'Veritabanı geri alınamadı: ' . $m['old_image'];
            }
        }

        // En iyi gayret: artık boşalmış yeni klasörü temizle.
        if (!empty($moved[0]['new_image'])) {
            $new_dir = dirname($image_dir . $moved[0]['new_image']);
            if (is_dir($new_dir)) {
                $remaining = array_diff(scandir($new_dir), array('.', '..'));
                if (!$remaining) { @rmdir($new_dir); }
            }
        }

        if ($errors) {
            return array('success' => false, 'error' => implode(' ', $errors));
        }

        return array('success' => true);
    }

    private function writeImagePath($source, $pk, $new_image) {
        if ($source === 'product') {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($new_image) . "' WHERE product_id = '" . (int)$pk . "'");
        } else {
            $this->db->query("UPDATE " . DB_PREFIX . "product_image SET image = '" . $this->db->escape($new_image) . "' WHERE product_image_id = '" . (int)$pk . "'");
        }
    }

    private function rollbackMoves($moved, $image_dir, $new_abs_dir) {
        foreach ($moved as $m) {
            $old_abs = $image_dir . $m['old_image'];
            $new_abs = $image_dir . $m['new_image'];
            if (is_file($new_abs)) { @rename($new_abs, $old_abs); }
        }
        if (is_dir($new_abs_dir)) {
            $remaining = array_diff(@scandir($new_abs_dir) ?: array(), array('.', '..'));
            if (!$remaining) { @rmdir($new_abs_dir); }
        }
    }

    public function writeLog($batch) {
        $dir = $this->getLogDir();
        if (!is_dir($dir)) { @mkdir($dir, 0755, true); }

        $filename = 'batch-' . date('Y-m-d_His') . '.json';
        $path = $dir . $filename;

        @file_put_contents($path, json_encode($batch, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return $filename;
    }

    public function getLatestLog() {
        $dir = $this->getLogDir();
        if (!is_dir($dir)) { return null; }

        $files = glob($dir . 'batch-*.json');
        if (!$files) { return null; }

        rsort($files);
        $content = @file_get_contents($files[0]);
        $data = $content ? json_decode($content, true) : null;

        if (!is_array($data)) { return null; }

        return array('filename' => basename($files[0]), 'entries' => $data);
    }

    public function markLogReverted($filename) {
        $dir = $this->getLogDir();
        $path = $dir . $filename;
        if (is_file($path)) { @rename($path, $path . '.reverted'); }
    }
}
