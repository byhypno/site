<?php
require_once(DIR_SYSTEM . 'library/egeser_lead_schema.php');

class ModelExtensionModuleEgeserLead extends Model {
    public function install() {
        EgeserLeadSchema::install($this->db);

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_lead_rate_limit` (
            `bucket_key` CHAR(64) NOT NULL,
            `hits` INT(11) NOT NULL DEFAULT 1,
            `window_started` DATETIME NOT NULL,
            `last_seen` DATETIME NOT NULL,
            PRIMARY KEY (`bucket_key`),
            KEY `idx_last_seen` (`last_seen`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
    }

    private function ipHash() {
        $ip = isset($this->request->server['REMOTE_ADDR']) ? (string)$this->request->server['REMOTE_ADDR'] : '';
        $salt = (string)$this->config->get('egeser_security_ip_salt');
        if (!$salt) $salt = 'egeser-lead';
        return hash('sha256', $salt . '|' . $ip);
    }

    public function rateLimitAllowed($limit = 5, $window_seconds = 600) {
        $this->install();

        $limit = max(2, min(50, (int)$limit));
        $window_seconds = max(60, min(86400, (int)$window_seconds));
        $bucket = hash('sha256', 'lead|' . $this->ipHash());

        $q = $this->db->query("SELECT hits,window_started FROM `" . DB_PREFIX . "egeser_lead_rate_limit`
            WHERE bucket_key='" . $this->db->escape($bucket) . "' LIMIT 1");

        if (!$q->num_rows) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_lead_rate_limit`
                SET bucket_key='" . $this->db->escape($bucket) . "',
                    hits=1, window_started=NOW(), last_seen=NOW()");
            return true;
        }

        $age = time() - strtotime($q->row['window_started']);
        if ($age >= $window_seconds) {
            $this->db->query("UPDATE `" . DB_PREFIX . "egeser_lead_rate_limit`
                SET hits=1, window_started=NOW(), last_seen=NOW()
                WHERE bucket_key='" . $this->db->escape($bucket) . "'");
            return true;
        }

        $hits = (int)$q->row['hits'] + 1;
        $this->db->query("UPDATE `" . DB_PREFIX . "egeser_lead_rate_limit`
            SET hits=" . $hits . ", last_seen=NOW()
            WHERE bucket_key='" . $this->db->escape($bucket) . "'");

        return $hits <= $limit;
    }

    public function addLead($data) {
        $this->install();

        $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_lead_log`
            SET customer_type='" . $this->db->escape($data['customer_type']) . "',
                company='" . $this->db->escape($data['company']) . "',
                name='" . $this->db->escape($data['name']) . "',
                phone='" . $this->db->escape($data['phone']) . "',
                email='" . $this->db->escape($data['email']) . "',
                project_type='" . $this->db->escape($data['project_type']) . "',
                location='" . $this->db->escape($data['location']) . "',
                area='" . $this->db->escape($data['area']) . "',
                product_id=" . (int)$data['product_id'] . ",
                product_name='" . $this->db->escape(utf8_substr($data['product_name'],0,255)) . "',
                message='" . $this->db->escape($data['message']) . "',
                source='" . $this->db->escape(utf8_substr($data['source'],0,255)) . "',
                page_url='" . $this->db->escape(utf8_substr($data['page_url'],0,700)) . "',
                utm_source='" . $this->db->escape(utf8_substr($data['utm_source'],0,100)) . "',
                utm_medium='" . $this->db->escape(utf8_substr($data['utm_medium'],0,100)) . "',
                utm_campaign='" . $this->db->escape(utf8_substr($data['utm_campaign'],0,150)) . "',
                mail_status='pending',
                mail_error='',
                consent=" . (int)$data['consent'] . ",
                date_added=NOW()");

        return (int)$this->db->getLastId();
    }

    public function updateMailStatus($lead_id, $status, $error) {
        $this->db->query("UPDATE `" . DB_PREFIX . "egeser_lead_log`
            SET mail_status='" . $this->db->escape($status) . "',
                mail_error='" . $this->db->escape(utf8_substr($error,0,500)) . "'
            WHERE lead_id=" . (int)$lead_id);
    }
}
