<?php
/* Sitedeki TÜM ürünlerin (herhangi bir kategori ayrımı olmadan) ana ve ek
   görsellerini doğrudan değiştirmeyi/eklemeyi/kaldırmayı sağlayan araç.
   Klasör yapısını yeniden düzenlemeye çalışmaz; yeni yüklenen görsel, o
   ürünün zaten kullandığı klasöre (yoksa ürün adına göre yeni bir klasöre)
   kaydedilir ve eski dosya, başka bir ürün tarafından kullanılmıyorsa
   silinir. */
class ModelCatalogEgeserImageManager extends Model {
    private $base_relative = 'catalog/urunler/';

    private $transliterate = array(
        'ç' => 'c', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
        'â' => 'a', 'î' => 'i', 'û' => 'u', '²' => '2'
    );

    private $allowed_extensions = array('jpg', 'jpeg', 'gif', 'png');

    private function slugify($s) {
        $s = mb_strtolower($s, 'UTF-8');
        $s = strtr($s, $this->transliterate);
        $s = preg_replace('/[^a-z0-9]+/', '-', $s);
        return trim($s, '-');
    }

    private function getImageDir() {
        return rtrim(DIR_IMAGE, '/') . '/';
    }

    public function getTotalProducts($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')";

        if (!empty($data['filter_name'])) {
            $sql .= " WHERE pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $query = $this->db->query($sql);

        return (int)$query->row['total'];
    }

    public function getProducts($data = array()) {
        $sql = "SELECT p.product_id, p.image, p.model, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')";

        if (!empty($data['filter_name'])) {
            $sql .= " WHERE pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sql .= " ORDER BY pd.name ASC";

        if (isset($data['start']) || isset($data['limit'])) {
            $start = isset($data['start']) ? (int)$data['start'] : 0;
            $limit = isset($data['limit']) ? (int)$data['limit'] : 40;
            if ($start < 0) { $start = 0; }
            if ($limit < 1) { $limit = 40; }
            $sql .= " LIMIT " . $start . "," . $limit;
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getProduct($product_id) {
        $query = $this->db->query("SELECT p.product_id, p.image, pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') WHERE p.product_id = '" . (int)$product_id . "'");

        return $query->num_rows ? $query->row : false;
    }

    public function getAdditionalImages($product_id) {
        $query = $this->db->query("SELECT product_image_id, image, sort_order FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC, product_image_id ASC");

        return $query->rows;
    }

    private function targetFolder($product_id, $product_name, $current_image) {
        if ($current_image) {
            return rtrim(dirname($current_image), '/');
        }

        $slug = $this->slugify($product_name);
        if ($slug === '') { $slug = 'urun'; }

        return rtrim($this->base_relative, '/') . '/' . $slug . '-' . $product_id;
    }

    private function safeFilename($target_abs_dir, $original_name) {
        $original_name = basename(html_entity_decode($original_name, ENT_QUOTES, 'UTF-8'));
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        if (!in_array($ext, $this->allowed_extensions, true)) {
            return false;
        }

        $slug = $this->slugify(pathinfo($original_name, PATHINFO_FILENAME));
        if ($slug === '') { $slug = 'gorsel'; }

        $candidate = $slug . '.' . $ext;
        $i = 1;

        while (is_file($target_abs_dir . '/' . $candidate)) {
            $candidate = $slug . '-' . $i . '.' . $ext;
            $i++;
        }

        return $candidate;
    }

    private function validateUploadedImage($tmp_name) {
        $info = @getimagesize($tmp_name);

        if (!$info) { return false; }

        return in_array($info[2], array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF), true);
    }

    private function isPathStillReferenced($path) {
        $path_esc = $this->db->escape($path);

        $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE image = '" . $path_esc . "' LIMIT 1");
        if ($query->num_rows) { return true; }

        $query = $this->db->query("SELECT product_image_id FROM " . DB_PREFIX . "product_image WHERE image = '" . $path_esc . "' LIMIT 1");
        if ($query->num_rows) { return true; }

        return false;
    }

    private function storeUpload($product_id, $product_name, $current_image, $tmp_name, $original_name) {
        if (!is_uploaded_file($tmp_name)) {
            return array('success' => false, 'error' => 'Geçersiz yükleme.');
        }

        if (!$this->validateUploadedImage($tmp_name)) {
            return array('success' => false, 'error' => 'Dosya geçerli bir görsel değil (jpg, png veya gif olmalı).');
        }

        $image_dir = $this->getImageDir();
        $target_rel_dir = $this->targetFolder($product_id, $product_name, $current_image);
        $target_abs_dir = $image_dir . $target_rel_dir;

        if (!is_dir($target_abs_dir) && !@mkdir($target_abs_dir, 0755, true)) {
            return array('success' => false, 'error' => 'Klasör oluşturulamadı: ' . $target_rel_dir);
        }

        $filename = $this->safeFilename($target_abs_dir, $original_name);
        if (!$filename) {
            return array('success' => false, 'error' => 'Desteklenmeyen dosya türü (jpg, jpeg, gif, png olmalı).');
        }

        $new_rel = $target_rel_dir . '/' . $filename;
        $new_abs = $image_dir . $new_rel;

        if (!move_uploaded_file($tmp_name, $new_abs)) {
            return array('success' => false, 'error' => 'Dosya sunucuya kaydedilemedi.');
        }

        return array('success' => true, 'image' => $new_rel);
    }

    /* Ana görseli değiştirir. Eski dosya başka bir üründe kullanılmıyorsa silinir. */
    public function replaceMainImage($product_id, $tmp_name, $original_name) {
        $product = $this->getProduct($product_id);
        if (!$product) {
            return array('success' => false, 'error' => 'Ürün bulunamadı.');
        }

        $stored = $this->storeUpload($product_id, $product['name'], $product['image'], $tmp_name, $original_name);
        if (!$stored['success']) {
            return $stored;
        }

        $new_rel = $stored['image'];
        $old_image = $product['image'];
        $image_dir = $this->getImageDir();

        try {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($new_rel) . "' WHERE product_id = '" . (int)$product_id . "'");
        } catch (Exception $e) {
            @unlink($image_dir . $new_rel);
            return array('success' => false, 'error' => 'Veritabanı güncellenemedi: ' . $e->getMessage());
        }

        if ($old_image && $old_image !== $new_rel && is_file($image_dir . $old_image) && !$this->isPathStillReferenced($old_image)) {
            @unlink($image_dir . $old_image);
        }

        return array('success' => true, 'product_id' => (int)$product_id, 'image' => $new_rel);
    }

    /* Ürüne yeni bir ek görsel ekler. */
    public function addAdditionalImage($product_id, $tmp_name, $original_name) {
        $product = $this->getProduct($product_id);
        if (!$product) {
            return array('success' => false, 'error' => 'Ürün bulunamadı.');
        }

        $stored = $this->storeUpload($product_id, $product['name'], $product['image'], $tmp_name, $original_name);
        if (!$stored['success']) {
            return $stored;
        }

        $new_rel = $stored['image'];
        $image_dir = $this->getImageDir();

        try {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($new_rel) . "', sort_order = '0'");
        } catch (Exception $e) {
            @unlink($image_dir . $new_rel);
            return array('success' => false, 'error' => 'Veritabanı güncellenemedi: ' . $e->getMessage());
        }

        $product_image_id = (int)$this->db->getLastId();

        return array('success' => true, 'product_id' => (int)$product_id, 'product_image_id' => $product_image_id, 'image' => $new_rel);
    }

    /* Bir ek görseli kaldırır. Dosya başka bir üründe kullanılmıyorsa silinir. */
    public function removeAdditionalImage($product_image_id) {
        $query = $this->db->query("SELECT product_id, image FROM " . DB_PREFIX . "product_image WHERE product_image_id = '" . (int)$product_image_id . "'");

        if (!$query->num_rows) {
            return array('success' => false, 'error' => 'Görsel bulunamadı.');
        }

        $row = $query->row;

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_image_id = '" . (int)$product_image_id . "'");

        $image_dir = $this->getImageDir();

        if ($row['image'] && is_file($image_dir . $row['image']) && !$this->isPathStillReferenced($row['image'])) {
            @unlink($image_dir . $row['image']);
        }

        return array('success' => true, 'product_image_id' => (int)$product_image_id);
    }
}
