<?php
/**
 * Egeser teklif formu (egeser_lead_log) icin tek sema kaynagi.
 * Onceden catalog/model/extension/module/egeser_lead.php,
 * admin/model/extension/module/egeser_lead_manager.php ve
 * admin/model/extension/module/egeser_health.php ayni tabloyu
 * bagimsiz olarak kuruyordu; bu sinif ucunu de tek noktada birlestirir.
 */
class EgeserLeadSchema {
    public static function install($db) {
        $db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_lead_log` (
            `lead_id` INT(11) NOT NULL AUTO_INCREMENT,
            `customer_type` VARCHAR(30) NOT NULL,
            `company` VARCHAR(150) NOT NULL DEFAULT '',
            `name` VARCHAR(80) NOT NULL,
            `phone` VARCHAR(30) NOT NULL,
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
            `lead_status` VARCHAR(30) NOT NULL DEFAULT 'Yeni',
            `admin_note` TEXT NOT NULL,
            `assigned_to` VARCHAR(100) NOT NULL DEFAULT '',
            `date_added` DATETIME NOT NULL,
            `date_modified` DATETIME NULL DEFAULT NULL,
            PRIMARY KEY (`lead_id`),
            KEY `idx_date_added` (`date_added`),
            KEY `idx_mail_status` (`mail_status`),
            KEY `idx_customer_type` (`customer_type`),
            KEY `idx_product_id` (`product_id`),
            KEY `idx_lead_status` (`lead_status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        self::ensureColumns($db);
    }

    public static function ensureColumns($db) {
        $columns = array(
            'location' => "VARCHAR(120) NOT NULL DEFAULT ''",
            'area' => "VARCHAR(40) NOT NULL DEFAULT ''",
            'product_id' => "INT(11) NOT NULL DEFAULT 0",
            'product_name' => "VARCHAR(255) NOT NULL DEFAULT ''",
            'message' => "TEXT NOT NULL",
            'page_url' => "VARCHAR(700) NOT NULL DEFAULT ''",
            'utm_medium' => "VARCHAR(100) NOT NULL DEFAULT ''",
            'utm_campaign' => "VARCHAR(150) NOT NULL DEFAULT ''",
            'lead_status' => "VARCHAR(30) NOT NULL DEFAULT 'Yeni'",
            'admin_note' => "TEXT NOT NULL",
            'assigned_to' => "VARCHAR(100) NOT NULL DEFAULT ''",
            'date_modified' => "DATETIME NULL DEFAULT NULL"
        );

        foreach ($columns as $column => $definition) {
            $q = $db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "egeser_lead_log` LIKE '" . $db->escape($column) . "'");
            if (!$q->num_rows) {
                $db->query("ALTER TABLE `" . DB_PREFIX . "egeser_lead_log` ADD `" . $column . "` " . $definition);
            }
        }

        $index = $db->query("SHOW INDEX FROM `" . DB_PREFIX . "egeser_lead_log` WHERE Key_name='idx_lead_status'");
        if (!$index->num_rows) {
            $db->query("ALTER TABLE `" . DB_PREFIX . "egeser_lead_log` ADD KEY `idx_lead_status` (`lead_status`)");
        }
    }
}
