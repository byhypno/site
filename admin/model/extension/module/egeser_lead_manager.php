<?php
require_once(DIR_SYSTEM . 'library/egeser_lead_schema.php');

class ModelExtensionModuleEgeserLeadManager extends Model {
    public function install() {
        EgeserLeadSchema::install($this->db);
    }
}
