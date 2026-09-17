<?php
class ModelExtensionModuleEgeserLeadSecurityV103 extends Model {
    public function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_csrf_nonce_v103` (
            `nonce_id` INT(11) NOT NULL AUTO_INCREMENT,
            `token_hash` CHAR(64) NOT NULL,
            `ua_hash` CHAR(64) NOT NULL DEFAULT '',
            `expires_at` DATETIME NOT NULL,
            `used` TINYINT(1) NOT NULL DEFAULT '0',
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`nonce_id`),
            UNIQUE KEY `token_hash` (`token_hash`),
            KEY `expires_used` (`expires_at`,`used`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }
}
