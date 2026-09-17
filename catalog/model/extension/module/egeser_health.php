<?php
class ModelExtensionModuleEgeserHealth extends Model {
    public function log404($url, $referrer = '') {
        $table = DB_PREFIX . 'egeser_404_log';
        $exists = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape($table) . "'");
        if (!$exists->num_rows) { return; }
        $url = utf8_substr(strip_tags((string)$url), 0, 500);
        $referrer = utf8_substr(strip_tags((string)$referrer), 0, 500);
        $q = $this->db->query("SELECT log_id FROM `" . $table . "` WHERE url='" . $this->db->escape($url) . "' LIMIT 1");
        if ($q->num_rows) {
            $this->db->query("UPDATE `" . $table . "` SET hits=hits+1,last_seen=NOW(),referrer='" . $this->db->escape($referrer) . "' WHERE log_id='" . (int)$q->row['log_id'] . "'");
        } else {
            $this->db->query("INSERT INTO `" . $table . "` SET url='" . $this->db->escape($url) . "',referrer='" . $this->db->escape($referrer) . "',hits=1,last_seen=NOW()");
        }
    }
}
