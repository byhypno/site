<?php
class ModelExtensionModuleEgeserFloorplan extends Model {
    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_product_floorplan` (
            `floorplan_id` int(11) NOT NULL AUTO_INCREMENT,
            `product_id` int(11) NOT NULL,
            `title` varchar(190) NOT NULL,
            `image` varchar(255) NOT NULL,
            `sort_order` int(11) NOT NULL DEFAULT '0',
            PRIMARY KEY (`floorplan_id`),
            KEY `product_id` (`product_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
    }

    public function savePlans($product_id, $plans) {
        $product_id = (int)$product_id;
        $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_product_floorplan` WHERE product_id = '" . $product_id . "'");
        if (!is_array($plans)) return;
        foreach ($plans as $plan) {
            $title = isset($plan['title']) ? trim($plan['title']) : '';
            $image = isset($plan['image']) ? trim($plan['image']) : '';
            $sort = isset($plan['sort_order']) ? (int)$plan['sort_order'] : 0;
            if ($title === '' || $image === '') continue;
            $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_product_floorplan` SET product_id='" . $product_id . "', title='" . $this->db->escape($title) . "', image='" . $this->db->escape($image) . "', sort_order='" . $sort . "'");
        }
    }

    public function getPlans($product_id) {
        $q = $this->db->query("SELECT * FROM `" . DB_PREFIX . "egeser_product_floorplan` WHERE product_id='" . (int)$product_id . "' ORDER BY sort_order ASC, floorplan_id ASC");
        return $q->rows;
    }

    public function getProducts() {
        $language_id = (int)$this->config->get('config_language_id');
        $q = $this->db->query("SELECT p.product_id, pd.name FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id=pd.product_id) WHERE pd.language_id='" . $language_id . "' ORDER BY pd.name ASC");
        return $q->rows;
    }
}
