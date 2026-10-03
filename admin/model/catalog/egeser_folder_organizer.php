<?php
/* EGESER - "image/catalog/urunler/<id>/" klasörlerini okunaklı isimlere
   taşıyan ve aynı anda veritabanındaki ilgili ürün resim yollarını
   güncelleyen araç. Önce sadece okur (scan), onay sonrası tek tek
   uygular (apply) ve her uygulamayı geri alınabilir şekilde loglar. */
class ModelCatalogEgeserFolderOrganizer extends Model {
    private $base_relative = 'catalog/urunler/';

    private function getBaseDir() {
        return rtrim(DIR_IMAGE, '/') . '/' . $this->base_relative;
    }

    private function getCacheBaseDir() {
        return rtrim(DIR_IMAGE, '/') . '/cache/' . $this->base_relative;
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

    private function rrmdir($dir) {
        if (!is_dir($dir)) { return; }
        $items = @scandir($dir);
        if (!$items) { return; }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') { continue; }
            $path = $dir . '/' . $item;
            if (is_dir($path)) { $this->rrmdir($path); } else { @unlink($path); }
        }
        @rmdir($dir);
    }

    /* Salt okunur keşif: diskteki sayısal klasörleri, her birinin
       hangi ürün(ler)e ait olduğunu ve önerilen yeni ismi döner.
       Hiçbir şeyi değiştirmez. */
    public function scan() {
        $base_dir = $this->getBaseDir();

        $result = array('base_dir_exists' => is_dir($base_dir), 'items' => array());

        if (!$result['base_dir_exists']) {
            return $result;
        }

        $disk_folders = array();
        foreach (scandir($base_dir) as $entry) {
            if ($entry === '.' || $entry === '..') { continue; }
            if (!is_dir($base_dir . $entry)) { continue; }
            if (!preg_match('/^[0-9]+$/', $entry)) { continue; } // sadece sayısal klasörler
            $disk_folders[] = $entry;
        }

        if (!$disk_folders) {
            return $result;
        }

        /* Bu klasörlerdeki görselleri hangi ürün(ler) kullanıyor? */
        $by_folder = array();

        $like = $this->db->escape($this->base_relative) . '%';

        $rows = array();
        $q1 = $this->db->query("SELECT product_id, image FROM " . DB_PREFIX . "product WHERE image LIKE '" . $like . "'");
        foreach ($q1->rows as $r) { $rows[] = $r; }
        $q2 = $this->db->query("SELECT product_id, image FROM " . DB_PREFIX . "product_image WHERE image LIKE '" . $like . "'");
        foreach ($q2->rows as $r) { $rows[] = $r; }

        foreach ($rows as $r) {
            if (!preg_match('#^' . preg_quote($this->base_relative, '#') . '([^/]+)/#', $r['image'], $m)) { continue; }
            $folder_id = $m[1];
            if (!isset($by_folder[$folder_id])) { $by_folder[$folder_id] = array('product_ids' => array(), 'image_count' => 0); }
            $by_folder[$folder_id]['product_ids'][(int)$r['product_id']] = true;
            $by_folder[$folder_id]['image_count']++;
        }

        /* İlgili ürün adlarını tek sorguda çek. */
        $all_product_ids = array();
        foreach ($by_folder as $info) {
            foreach (array_keys($info['product_ids']) as $pid) { $all_product_ids[$pid] = true; }
        }

        $names = array();
        if ($all_product_ids) {
            $ids = implode(',', array_map('intval', array_keys($all_product_ids)));
            $qn = $this->db->query("SELECT product_id, name FROM " . DB_PREFIX . "product_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND product_id IN (" . $ids . ")");
            foreach ($qn->rows as $r) { $names[(int)$r['product_id']] = $r['name']; }
        }

        $used_slugs = array();

        foreach ($disk_folders as $folder_id) {
            $item = array(
                'folder_id'    => $folder_id,
                'status'       => '',
                'product_id'   => null,
                'product_name' => '',
                'new_slug'     => '',
                'image_count'  => isset($by_folder[$folder_id]) ? $by_folder[$folder_id]['image_count'] : 0,
                'note'         => ''
            );

            if (!isset($by_folder[$folder_id])) {
                $item['status'] = 'orphan';
                $item['note'] = 'Veritabanında bu klasörü kullanan ürün bulunamadı.';
            } else {
                $product_ids = array_keys($by_folder[$folder_id]['product_ids']);

                if (count($product_ids) > 1) {
                    $item['status'] = 'shared';
                    $item['note'] = count($product_ids) . ' farklı ürün bu klasördeki görselleri kullanıyor, güvenlik için atlandı.';
                } else {
                    $pid = $product_ids[0];
                    $name = isset($names[$pid]) ? $names[$pid] : ('urun-' . $pid);
                    $slug = $this->slugify($name);
                    if ($slug === '') { $slug = 'urun'; }
                    $new_slug = $slug . '-' . $folder_id;

                    if (is_dir($this->getBaseDir() . $new_slug) || isset($used_slugs[$new_slug])) {
                        $item['status'] = 'conflict';
                        $item['note'] = 'Hedef klasör adı zaten var: ' . $new_slug;
                    } else {
                        $item['status'] = 'ready';
                        $item['product_id'] = $pid;
                        $item['product_name'] = $name;
                        $item['new_slug'] = $new_slug;
                        $used_slugs[$new_slug] = true;
                    }
                }
            }

            $result['items'][] = $item;
        }

        return $result;
    }

    /* Tek bir klasörü yeniden adlandırır + ilgili DB satırlarını
       günceller. Klasör adımı başarılı olup DB adımı başarısız
       olursa klasörü eski haline geri döndürür (telafi edici işlem).
       $from / $to: diskteki klasör adları (numara veya slug fark etmez). */
    private function renameFolder($from, $to, $cache_cleanup_id) {
        if (!preg_match('/^[a-z0-9-]+$/i', $from) || $from === '') {
            return array('success' => false, 'error' => 'Geçersiz kaynak klasör adı.');
        }
        if (!preg_match('/^[a-z0-9-]+$/i', $to) || $to === '') {
            return array('success' => false, 'error' => 'Geçersiz hedef klasör adı.');
        }

        $base_dir = $this->getBaseDir();
        $old_dir = $base_dir . $from;
        $new_dir = $base_dir . $to;

        $old_rel = $this->base_relative . $from;
        $new_rel = $this->base_relative . $to;

        if (!is_dir($old_dir)) {
            return array('success' => false, 'error' => 'Kaynak klasör bulunamadı: ' . $old_rel);
        }
        if (is_dir($new_dir) || file_exists($new_dir)) {
            return array('success' => false, 'error' => 'Hedef klasör zaten var: ' . $new_rel);
        }

        if (!@rename($old_dir, $new_dir)) {
            return array('success' => false, 'error' => 'Klasör yeniden adlandırılamadı (sunucu yazma izni?): ' . $old_rel);
        }

        $old_prefix = $this->db->escape($old_rel . '/');
        $new_prefix = $this->db->escape($new_rel . '/');

        try {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET image = REPLACE(image, '" . $old_prefix . "', '" . $new_prefix . "') WHERE image LIKE '" . $old_prefix . "%'");
            $this->db->query("UPDATE " . DB_PREFIX . "product_image SET image = REPLACE(image, '" . $old_prefix . "', '" . $new_prefix . "') WHERE image LIKE '" . $old_prefix . "%'");
        } catch (Exception $e) {
            // Telafi: klasörü eski adına geri al, veri tabanı eski haliyle tutarlı kalsın.
            @rename($new_dir, $old_dir);
            return array('success' => false, 'error' => 'Veritabanı güncellenemedi, klasör adı geri alındı: ' . $e->getMessage());
        }

        // En iyi gayret: eski önbellek klasörünü temizle (kritik değil).
        $cache_dir = $this->getCacheBaseDir() . $cache_cleanup_id;
        if (is_dir($cache_dir)) { $this->rrmdir($cache_dir); }

        return array('success' => true, 'old_relative' => $old_rel, 'new_relative' => $new_rel);
    }

    public function applyOne($folder_id, $new_slug, $product_id) {
        if (!preg_match('/^[0-9]+$/', $folder_id)) {
            return array('success' => false, 'error' => 'Geçersiz klasör kimliği.');
        }

        $outcome = $this->renameFolder($folder_id, $new_slug, $folder_id);

        if (!$outcome['success']) { return $outcome; }

        return array(
            'success'      => true,
            'folder_id'    => $folder_id,
            'new_slug'     => $new_slug,
            'product_id'   => $product_id,
            'old_relative' => $outcome['old_relative'],
            'new_relative' => $outcome['new_relative']
        );
    }

    /* folder_id'yi new_slug'a geri döndürür (geri alma işlemi). */
    public function revertOne($folder_id, $new_slug) {
        if (!preg_match('/^[0-9]+$/', $folder_id)) {
            return array('success' => false, 'error' => 'Geçersiz klasör kimliği.');
        }

        return $this->renameFolder($new_slug, $folder_id, $new_slug);
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
