<?php
class ModelExtensionModuleEgeserLeadSecurityFinal extends Model {
    public function issueNonce() {
        $this->ensureTable();
        $this->prune();

        $token = $this->randomToken();
        $hash = hash('sha256', $token);
        $ua = $this->userAgentHash();

        $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_csrf_nonce_final`
            SET `token_hash`='" . $this->db->escape($hash) . "',
                `ua_hash`='" . $this->db->escape($ua) . "',
                `expires_at`=DATE_ADD(NOW(), INTERVAL 20 MINUTE),
                `used`='0',
                `created_at`=NOW()");

        return $token;
    }

    public function verifyNonce($token) {
        if (!is_string($token) || strlen($token) < 40 || strlen($token) > 160) return false;

        $this->ensureTable();
        $hash = hash('sha256', $token);
        $ua = $this->userAgentHash();

        $q = $this->db->query("SELECT `nonce_id` FROM `" . DB_PREFIX . "egeser_csrf_nonce_final`
            WHERE `token_hash`='" . $this->db->escape($hash) . "'
              AND `ua_hash`='" . $this->db->escape($ua) . "'
              AND `used`='0'
              AND `expires_at`>=NOW()
            LIMIT 1");

        return (bool)$q->num_rows;
    }

    public function consumeNonce($token) {
        if (!$this->verifyNonce($token)) return false;

        $hash = hash('sha256', $token);
        $ua = $this->userAgentHash();

        $this->db->query("UPDATE `" . DB_PREFIX . "egeser_csrf_nonce_final`
            SET `used`='1'
            WHERE `token_hash`='" . $this->db->escape($hash) . "'
              AND `ua_hash`='" . $this->db->escape($ua) . "'
              AND `used`='0'
              AND `expires_at`>=NOW()
            LIMIT 1");

        return $this->db->countAffected() === 1;
    }

    private function ensureTable() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_csrf_nonce_final` (
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

    private function prune() {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_csrf_nonce_final`
            WHERE `expires_at` < NOW()
               OR (`used`='1' AND `created_at` < DATE_SUB(NOW(), INTERVAL 1 HOUR))");
    }

    private function userAgentHash() {
        $ua = isset($this->request->server['HTTP_USER_AGENT']) ? (string)$this->request->server['HTTP_USER_AGENT'] : '';
        return hash('sha256', utf8_substr($ua, 0, 500));
    }

    private function randomToken() {
        if (function_exists('random_bytes')) {
            try {
                return bin2hex(random_bytes(32));
            } catch (Exception $e) {}
        }

        return sha1(uniqid(mt_rand(), true)) . sha1(uniqid(mt_rand(), true));
    }
}
