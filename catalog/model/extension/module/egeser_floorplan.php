<?php
class ModelExtensionModuleEgeserFloorplan extends Model {
    public function getPlans($product_id) {
        $q = $this->db->query("SELECT * FROM `" . DB_PREFIX . "egeser_product_floorplan` WHERE product_id='" . (int)$product_id . "' ORDER BY sort_order ASC, floorplan_id ASC");
        return $q->rows;
    }
}
