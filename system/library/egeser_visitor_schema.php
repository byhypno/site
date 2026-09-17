<?php
/**
 * Egeser Ziyaretci & Lead Takip Merkezi - veritabani semasi.
 * Diger egeser_* semalari gibi (bkz. egeser_lead_schema.php) tek
 * kaynaktan kurulur; catalog ve admin taraflari bu sinifi cagirir.
 */
class EgeserVisitorSchema {
    public static function install($db) {
        $db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_visitor` (
            `visitor_id` INT(11) NOT NULL AUTO_INCREMENT,
            `visitor_token` CHAR(48) NOT NULL,
            `first_seen` DATETIME NOT NULL,
            `last_seen` DATETIME NOT NULL,
            `first_source` VARCHAR(60) NOT NULL DEFAULT '',
            `first_medium` VARCHAR(60) NOT NULL DEFAULT '',
            `first_campaign` VARCHAR(150) NOT NULL DEFAULT '',
            `first_referrer` VARCHAR(700) NOT NULL DEFAULT '',
            `first_landing_page` VARCHAR(700) NOT NULL DEFAULT '',
            `device_type` VARCHAR(20) NOT NULL DEFAULT '',
            `browser` VARCHAR(30) NOT NULL DEFAULT '',
            `os` VARCHAR(30) NOT NULL DEFAULT '',
            `country_code` VARCHAR(5) NOT NULL DEFAULT '',
            `city` VARCHAR(100) NOT NULL DEFAULT '',
            `is_bot` TINYINT(1) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            PRIMARY KEY (`visitor_id`),
            UNIQUE KEY `idx_visitor_token` (`visitor_token`),
            KEY `idx_last_seen` (`last_seen`),
            KEY `idx_first_source` (`first_source`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        $db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_session` (
            `session_id` INT(11) NOT NULL AUTO_INCREMENT,
            `session_token` CHAR(48) NOT NULL,
            `visitor_id` INT(11) NOT NULL,
            `started_at` DATETIME NOT NULL,
            `last_activity` DATETIME NOT NULL,
            `source` VARCHAR(60) NOT NULL DEFAULT '',
            `medium` VARCHAR(60) NOT NULL DEFAULT '',
            `campaign` VARCHAR(150) NOT NULL DEFAULT '',
            `term` VARCHAR(150) NOT NULL DEFAULT '',
            `content` VARCHAR(150) NOT NULL DEFAULT '',
            `referrer` VARCHAR(700) NOT NULL DEFAULT '',
            `landing_page` VARCHAR(700) NOT NULL DEFAULT '',
            `gclid` VARCHAR(150) NOT NULL DEFAULT '',
            `fbclid` VARCHAR(150) NOT NULL DEFAULT '',
            `device_type` VARCHAR(20) NOT NULL DEFAULT '',
            `browser` VARCHAR(30) NOT NULL DEFAULT '',
            `os` VARCHAR(30) NOT NULL DEFAULT '',
            `pageviews` INT(11) NOT NULL DEFAULT 0,
            `converted` TINYINT(1) NOT NULL DEFAULT 0,
            `lead_id` INT(11) NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`session_id`),
            UNIQUE KEY `idx_session_token` (`session_token`),
            KEY `idx_visitor_id` (`visitor_id`),
            KEY `idx_last_activity` (`last_activity`),
            KEY `idx_source` (`source`),
            KEY `idx_lead_id` (`lead_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        $db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_event` (
            `event_id` INT(11) NOT NULL AUTO_INCREMENT,
            `visitor_id` INT(11) NOT NULL,
            `session_id` INT(11) NOT NULL,
            `event_type` VARCHAR(30) NOT NULL,
            `page_url` VARCHAR(700) NOT NULL DEFAULT '',
            `page_title` VARCHAR(255) NOT NULL DEFAULT '',
            `route` VARCHAR(150) NOT NULL DEFAULT '',
            `entity_type` VARCHAR(30) NOT NULL DEFAULT '',
            `entity_id` INT(11) NOT NULL DEFAULT 0,
            `entity_name` VARCHAR(255) NOT NULL DEFAULT '',
            `referrer` VARCHAR(700) NOT NULL DEFAULT '',
            `event_value` VARCHAR(255) NOT NULL DEFAULT '',
            `metadata_json` TEXT NOT NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`event_id`),
            KEY `idx_visitor_id` (`visitor_id`),
            KEY `idx_session_id` (`session_id`),
            KEY `idx_created_at` (`created_at`),
            KEY `idx_event_type` (`event_type`),
            KEY `idx_entity` (`entity_type`,`entity_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        $db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_conversion` (
            `conversion_id` INT(11) NOT NULL AUTO_INCREMENT,
            `visitor_id` INT(11) NOT NULL,
            `session_id` INT(11) NOT NULL,
            `lead_id` INT(11) NOT NULL DEFAULT 0,
            `conversion_type` VARCHAR(30) NOT NULL,
            `entity_type` VARCHAR(30) NOT NULL DEFAULT '',
            `entity_id` INT(11) NOT NULL DEFAULT 0,
            `source` VARCHAR(60) NOT NULL DEFAULT '',
            `medium` VARCHAR(60) NOT NULL DEFAULT '',
            `campaign` VARCHAR(150) NOT NULL DEFAULT '',
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`conversion_id`),
            KEY `idx_visitor_id` (`visitor_id`),
            KEY `idx_session_id` (`session_id`),
            KEY `idx_lead_id` (`lead_id`),
            KEY `idx_conversion_type` (`conversion_type`),
            KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
    }
}
