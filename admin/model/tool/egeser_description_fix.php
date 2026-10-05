<?php
class ModelToolEgeserDescriptionFix extends Model {
    private $language_id = 0;

    public function fixText($text) {
        $text = preg_replace('/([a-zçğıöşü])([0-9]{1,4} ?m²)/u', '$1 $2', $text);
        $text = preg_replace('/\.([A-ZÇĞİÖŞÜ])/u', '. $1', $text);
        return $text;
    }

    public function getAffectedProducts() {
        $lang = $this->getLanguageId();
        $query = $this->db->query("SELECT product_id, name, description FROM " . DB_PREFIX . "product_description WHERE language_id = '" . (int)$lang . "'");

        $result = array();
        foreach ($query->rows as $row) {
            $fixed = $this->fixText($row['description']);
            if ($fixed !== $row['description']) {
                $result[] = array(
                    'product_id'  => (int)$row['product_id'],
                    'name'        => $row['name'],
                    'description' => $row['description'],
                    'fixed'       => $fixed
                );
            }
        }
        return $result;
    }

    public function applyFix($product_ids) {
        $lang = $this->getLanguageId();
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "product_description_yedek_" . date('Ymd') . "` AS SELECT * FROM " . DB_PREFIX . "product_description WHERE language_id = '" . (int)$lang . "'");

        $updated = 0;
        foreach ($product_ids as $product_id) {
            $product_id = (int)$product_id;
            $query = $this->db->query("SELECT description FROM " . DB_PREFIX . "product_description WHERE product_id = '" . $product_id . "' AND language_id = '" . (int)$lang . "'");
            if (!$query->num_rows) continue;

            $fixed = $this->fixText($query->row['description']);
            if ($fixed !== $query->row['description']) {
                $this->db->query("UPDATE " . DB_PREFIX . "product_description SET description = '" . $this->db->escape($fixed) . "' WHERE product_id = '" . $product_id . "' AND language_id = '" . (int)$lang . "'");
                $updated++;
            }
        }
        return $updated;
    }

    private function getLanguageId() {
        if ($this->language_id) return $this->language_id;
        $id = (int)$this->config->get('config_language_id');
        if ($id) { $this->language_id = $id; return $id; }
        $code = $this->config->get('config_language'); if (!$code) $code = 'tr-tr';
        $q = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code='" . $this->db->escape($code) . "' LIMIT 1");
        $this->language_id = $q->num_rows ? (int)$q->row['language_id'] : 2;
        return $this->language_id;
    }
}
