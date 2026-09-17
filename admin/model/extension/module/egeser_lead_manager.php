<?php
class ModelExtensionModuleEgeserLeadManager extends Model {
    public function install() {
        $this->ensureLeadTable();
        $this->ensureColumns();
    }

    private function ensureLeadTable() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_lead_log` (
            `lead_id` INT(11) NOT NULL AUTO_INCREMENT,
            `customer_type` VARCHAR(30) NOT NULL DEFAULT '',
            `company` VARCHAR(150) NOT NULL DEFAULT '',
            `name` VARCHAR(80) NOT NULL DEFAULT '',
            `phone` VARCHAR(30) NOT NULL DEFAULT '',
            `email` VARCHAR(120) NOT NULL DEFAULT '',
            `project_type` VARCHAR(100) NOT NULL DEFAULT '',
            `location` VARCHAR(120) NOT NULL DEFAULT '',
            `area` VARCHAR(40) NOT NULL DEFAULT '',
            `product_id` INT(11) NOT NULL DEFAULT 0,
            `product_name` VARCHAR(255) NOT NULL DEFAULT '',
            `message` TEXT NOT NULL,
            `source` VARCHAR(255) NOT NULL DEFAULT '',
            `page_url` VARCHAR(700) NOT NULL DEFAULT '',
            `utm_source` VARCHAR(100) NOT NULL DEFAULT '',
            `utm_medium` VARCHAR(100) NOT NULL DEFAULT '',
            `utm_campaign` VARCHAR(150) NOT NULL DEFAULT '',
            `mail_status` VARCHAR(20) NOT NULL DEFAULT 'pending',
            `mail_error` VARCHAR(500) NOT NULL DEFAULT '',
            `consent` TINYINT(1) NOT NULL DEFAULT 0,
            `date_added` DATETIME NOT NULL,
            PRIMARY KEY (`lead_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
    }

    private function ensureColumns() {
        $columns = array(
            'lead_status' => "VARCHAR(30) NOT NULL DEFAULT 'Yeni'",
            'admin_note' => "TEXT NOT NULL",
            'assigned_to' => "VARCHAR(100) NOT NULL DEFAULT ''",
            'date_modified' => "DATETIME NULL DEFAULT NULL"
        );

        foreach ($columns as $column => $definition) {
            $q = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "egeser_lead_log` LIKE '" . $this->db->escape($column) . "'");
            if (!$q->num_rows) {
                $this->db->query("ALTER TABLE `" . DB_PREFIX . "egeser_lead_log` ADD `" . $column . "` " . $definition);
            }
        }

        $indexes = $this->db->query("SHOW INDEX FROM `" . DB_PREFIX . "egeser_lead_log` WHERE Key_name='idx_lead_status'");
        if (!$indexes->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "egeser_lead_log` ADD KEY `idx_lead_status` (`lead_status`)");
        }
    }
}
