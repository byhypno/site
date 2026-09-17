<?php
/**
 * Egeser Security & SEO Spam Monitor
 * OpenCart 2.x compatible.
 *
 * Not a WAF. It provides application-level logging, basic rate limiting,
 * search noindex hardening and redirect-chain diagnostics.
 */
class EgeserSecurityMonitor {
    private $registry;
    private $db;
    private $config;

    public function __construct($registry) {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
    }

    public function installTables() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_search_spam_log` (
            `spam_id` INT(11) NOT NULL AUTO_INCREMENT,
            `query_hash` CHAR(40) NOT NULL,
            `query_text` VARCHAR(500) NOT NULL,
            `ip_hash` CHAR(64) NOT NULL,
            `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
            `hits` INT(11) NOT NULL DEFAULT 1,
            `first_seen` DATETIME NOT NULL,
            `last_seen` DATETIME NOT NULL,
            PRIMARY KEY (`spam_id`),
            UNIQUE KEY `idx_query_hash` (`query_hash`),
            KEY `idx_last_seen` (`last_seen`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_rate_limit` (
            `bucket_key` CHAR(64) NOT NULL,
            `hits` INT(11) NOT NULL DEFAULT 1,
            `window_started` DATETIME NOT NULL,
            `last_seen` DATETIME NOT NULL,
            PRIMARY KEY (`bucket_key`),
            KEY `idx_rate_last_seen` (`last_seen`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_redirect_audit` (
            `audit_id` INT(11) NOT NULL AUTO_INCREMENT,
            `source_url` VARCHAR(700) NOT NULL,
            `final_url` VARCHAR(700) NOT NULL DEFAULT '',
            `status` VARCHAR(20) NOT NULL DEFAULT 'unknown',
            `hop_count` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
            `chain_json` MEDIUMTEXT NOT NULL,
            `date_checked` DATETIME NOT NULL,
            PRIMARY KEY (`audit_id`),
            KEY `idx_redirect_checked` (`date_checked`),
            KEY `idx_redirect_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
    }

    private function ipHash() {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : '';
        $salt = (string)$this->config->get('egeser_security_ip_salt');
        if (!$salt) $salt = 'egeser';
        return hash('sha256', $salt . '|' . $ip);
    }

    public function isSuspiciousSearch($query) {
        $q = trim((string)$query);
        if ($q === '') return false;

        $patterns = array(
            '/\bcvv\b/i',
            '/\bcc\s?shop\b/i',
            '/\bcard(?:ing| dumps?)?\b/i',
            '/\bbank logins?\b/i',
            '/\bfullz\b/i',
            '/\bdumps?\b/i',
            '/\bamsiga\b/i',
            '/\bbase\d{2,}\b/i',
            '/\bcasino\b/i',
            '/\bviagra\b/i',
            '/\bpharmacy\b/i',
            '/\bcredit card\b/i'
        );

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $q)) return true;
        }

        // Çok uzun, anlamsız query parametreleri de şüpheli sayılır.
        if (utf8_strlen($q) > 180) return true;

        return false;
    }

    public function logSuspiciousSearch($query) {
        $this->installTables();

        $query = utf8_substr(trim((string)$query), 0, 500);
        if (!$query) return;

        $normalized_query = function_exists('utf8_strtolower') ? utf8_strtolower($query) : strtolower($query);
        $hash = sha1($normalized_query);
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? utf8_substr((string)$_SERVER['HTTP_USER_AGENT'], 0, 255) : '';
        $ip_hash = $this->ipHash();

        $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_search_spam_log`
            SET query_hash='" . $this->db->escape($hash) . "',
                query_text='" . $this->db->escape($query) . "',
                ip_hash='" . $this->db->escape($ip_hash) . "',
                user_agent='" . $this->db->escape($ua) . "',
                hits=1,
                first_seen=NOW(),
                last_seen=NOW()
            ON DUPLICATE KEY UPDATE
                hits=hits+1,
                last_seen=NOW(),
                user_agent=VALUES(user_agent),
                ip_hash=VALUES(ip_hash)");
    }

    public function rateLimit($scope, $limit = 30, $window_seconds = 60) {
        $this->installTables();

        $limit = max(5, min(300, (int)$limit));
        $window_seconds = max(10, min(3600, (int)$window_seconds));

        $bucket = hash('sha256', $scope . '|' . $this->ipHash());
        $q = $this->db->query("SELECT hits,window_started FROM `" . DB_PREFIX . "egeser_rate_limit`
            WHERE bucket_key='" . $this->db->escape($bucket) . "' LIMIT 1");

        if (!$q->num_rows) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_rate_limit`
                SET bucket_key='" . $this->db->escape($bucket) . "',
                    hits=1, window_started=NOW(), last_seen=NOW()");
            return true;
        }

        $age = time() - strtotime($q->row['window_started']);
        if ($age >= $window_seconds) {
            $this->db->query("UPDATE `" . DB_PREFIX . "egeser_rate_limit`
                SET hits=1, window_started=NOW(), last_seen=NOW()
                WHERE bucket_key='" . $this->db->escape($bucket) . "'");
            return true;
        }

        $hits = (int)$q->row['hits'] + 1;
        $this->db->query("UPDATE `" . DB_PREFIX . "egeser_rate_limit`
            SET hits=" . $hits . ", last_seen=NOW()
            WHERE bucket_key='" . $this->db->escape($bucket) . "'");

        return $hits <= $limit;
    }

    public function getSpamStats() {
        $this->installTables();

        $q = $this->db->query("SELECT
            COALESCE(SUM(last_seen >= DATE_SUB(NOW(), INTERVAL 1 DAY)),0) AS queries_24h,
            COALESCE(SUM(hits),0) AS total_hits,
            COUNT(*) AS unique_queries
            FROM `" . DB_PREFIX . "egeser_search_spam_log`");

        return array_map('intval', $q->row);
    }

    public function getRecentSpam($limit = 25) {
        $this->installTables();
        $limit = max(1, min(100, (int)$limit));

        $rows = $this->db->query("SELECT spam_id,query_text,hits,last_seen
            FROM `" . DB_PREFIX . "egeser_search_spam_log`
            ORDER BY last_seen DESC LIMIT " . $limit)->rows;

        return $rows;
    }

    private function baseHost() {
        $url = $this->config->get('config_ssl') ? $this->config->get('config_ssl') : $this->config->get('config_url');
        return strtolower((string)parse_url($url, PHP_URL_HOST));
    }

    private function normalizedHost($host) {
        return preg_replace('/^www\./i', '', strtolower((string)$host));
    }

    private function fetchHeaders($url) {
        $result = array('code'=>0, 'location'=>'', 'error'=>'');

        $host = strtolower((string)parse_url($url, PHP_URL_HOST));
        if (!$host || $this->normalizedHost($host) !== $this->normalizedHost($this->baseHost())) {
            $result['error'] = 'cross-host blocked';
            return $result;
        }

        if (function_exists('curl_init')) {
            $headers = array();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'EgeserRedirectBot/1.0');
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $line) use (&$headers) {
                $len = strlen($line);
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) $headers[strtolower(trim($parts[0]))] = trim($parts[1]);
                return $len;
            });
            curl_exec($ch);
            $result['code'] = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $result['location'] = isset($headers['location']) ? $headers['location'] : '';
            if (curl_errno($ch)) $result['error'] = curl_error($ch);
            curl_close($ch);

            if (in_array($result['code'], array(0,403,405), true)) {
                $headers = array();
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_RANGE, '0-2048');
                curl_setopt($ch, CURLOPT_USERAGENT, 'EgeserRedirectBot/1.0');
                curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $line) use (&$headers) {
                    $len = strlen($line);
                    $parts = explode(':', $line, 2);
                    if (count($parts) === 2) $headers[strtolower(trim($parts[0]))] = trim($parts[1]);
                    return $len;
                });
                curl_exec($ch);
                $result['code'] = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $result['location'] = isset($headers['location']) ? $headers['location'] : '';
                $result['error'] = curl_errno($ch) ? curl_error($ch) : '';
                curl_close($ch);
            }

            return $result;
        }

        $result['error'] = 'curl unavailable';
        return $result;
    }

    private function absolutize($base, $location) {
        if (preg_match('#^https?://#i', $location)) return $location;
        $parts = parse_url($base);
        if (substr($location, 0, 1) === '/') {
            return $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '') . $location;
        }
        $path = isset($parts['path']) ? dirname($parts['path']) : '/';
        return $parts['scheme'] . '://' . $parts['host'] . rtrim($path, '/') . '/' . $location;
    }

    public function auditRedirect($url, $max_hops = 8) {
        $this->installTables();

        $max_hops = max(2, min(12, (int)$max_hops));
        $chain = array();
        $seen = array();
        $current = $url;
        $status = 'ok';
        $final = $url;

        for ($i=0; $i<$max_hops; $i++) {
            if (isset($seen[$current])) {
                $status = 'loop';
                break;
            }
            $seen[$current] = true;

            $r = $this->fetchHeaders($current);
            $chain[] = array('url'=>$current,'code'=>$r['code'],'location'=>$r['location'],'error'=>$r['error']);

            if ($r['error']) {
                $status = 'error';
                break;
            }

            if (in_array($r['code'], array(301,302,303,307,308), true) && $r['location']) {
                $current = $this->absolutize($current, $r['location']);
                $final = $current;
                continue;
            }

            if ($r['code'] >= 400 || $r['code'] === 0) $status = 'error';
            break;
        }

        if (count($chain) >= $max_hops && in_array(end($chain)['code'], array(301,302,303,307,308), true)) {
            $status = 'too_many_hops';
        }

        $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_redirect_audit`
            SET source_url='" . $this->db->escape(utf8_substr($url,0,700)) . "',
                final_url='" . $this->db->escape(utf8_substr($final,0,700)) . "',
                status='" . $this->db->escape($status) . "',
                hop_count=" . max(0, count($chain)-1) . ",
                chain_json='" . $this->db->escape(json_encode($chain, JSON_UNESCAPED_UNICODE)) . "',
                date_checked=NOW()");

        return array(
            'status'=>$status,
            'final_url'=>$final,
            'hop_count'=>max(0, count($chain)-1),
            'chain'=>$chain
        );
    }

    public function getRecentRedirectAudits($limit = 20) {
        $this->installTables();
        $limit = max(1, min(100, (int)$limit));

        return $this->db->query("SELECT audit_id,source_url,final_url,status,hop_count,date_checked
            FROM `" . DB_PREFIX . "egeser_redirect_audit`
            ORDER BY audit_id DESC LIMIT " . $limit)->rows;
    }

    public function cleanup($days = 90) {
        $this->installTables();
        $days = max(30, min(730, (int)$days));

        $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_search_spam_log`
            WHERE last_seen < DATE_SUB(NOW(), INTERVAL " . $days . " DAY)");

        $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_rate_limit`
            WHERE last_seen < DATE_SUB(NOW(), INTERVAL 2 DAY)");

        $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_redirect_audit`
            WHERE date_checked < DATE_SUB(NOW(), INTERVAL " . $days . " DAY)");
    }
}
