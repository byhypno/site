<?php
/**
 * Egeser Health Monitor
 * OpenCart 2.x compatible shared service.
 *
 * Security:
 * - Same-origin scanner only.
 * - No admin/account/checkout URLs are crawled.
 * - No external URL fetching.
 * - Scan limits are hard capped.
 */
class EgeserHealthMonitor {
    private $registry;
    private $db;
    private $config;

    public function __construct($registry) {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
    }

    private function tableExists($table) {
        $query = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape(DB_PREFIX . $table) . "'");
        return (bool)$query->num_rows;
    }

    public function installTables() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_health_run` (
            `run_id` INT(11) NOT NULL AUTO_INCREMENT,
            `run_type` VARCHAR(20) NOT NULL DEFAULT 'manual',
            `status` VARCHAR(20) NOT NULL DEFAULT 'running',
            `score` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
            `pages_scanned` INT(11) NOT NULL DEFAULT 0,
            `links_checked` INT(11) NOT NULL DEFAULT 0,
            `issues_found` INT(11) NOT NULL DEFAULT 0,
            `details` MEDIUMTEXT NOT NULL,
            `date_started` DATETIME NOT NULL,
            `date_finished` DATETIME NULL,
            PRIMARY KEY (`run_id`),
            KEY `idx_run_date` (`date_started`),
            KEY `idx_run_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_link_issue` (
            `issue_id` INT(11) NOT NULL AUTO_INCREMENT,
            `issue_hash` CHAR(40) NOT NULL,
            `source_url` VARCHAR(700) NOT NULL,
            `target_url` VARCHAR(700) NOT NULL,
            `issue_type` VARCHAR(30) NOT NULL DEFAULT 'link',
            `http_code` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0,
            `hits` INT(11) NOT NULL DEFAULT 1,
            `first_seen` DATETIME NOT NULL,
            `last_seen` DATETIME NOT NULL,
            `is_open` TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (`issue_id`),
            UNIQUE KEY `idx_issue_hash` (`issue_hash`),
            KEY `idx_issue_open` (`is_open`),
            KEY `idx_issue_seen` (`last_seen`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
    }

    private function item($label, $status, $detail) {
        return array('label' => $label, 'status' => $status, 'detail' => $detail);
    }

    private function validTrackingId($value, $type) {
        $value = trim((string)$value);
        if ($type === 'ga4') return (bool)preg_match('/^G-[A-Z0-9]+$/i', $value);
        if ($type === 'meta') return (bool)preg_match('/^[0-9]{5,20}$/', $value);
        if ($type === 'ads') return (bool)preg_match('/^AW-[0-9]+$/i', $value);
        if ($type === 'label') return (bool)preg_match('/^[A-Za-z0-9_-]+$/', $value);
        return false;
    }

    public function getReport() {
        $report = array();

        $theme_files = array(
            'header.tpl' => DIR_CATALOG . 'view/theme/egeser/template/common/header.tpl',
            'home.tpl' => DIR_CATALOG . 'view/theme/egeser/template/common/home.tpl',
            'footer.tpl' => DIR_CATALOG . 'view/theme/egeser/template/common/footer.tpl',
            'category.tpl' => DIR_CATALOG . 'view/theme/egeser/template/product/category.tpl',
            'product.tpl' => DIR_CATALOG . 'view/theme/egeser/template/product/product.tpl',
            'theme.css' => DIR_CATALOG . 'view/theme/egeser/stylesheet/theme.css',
            'theme.js' => DIR_CATALOG . 'view/theme/egeser/javascript/theme.js'
        );
        $missing = array();
        foreach ($theme_files as $name => $path) {
            if (!is_file($path)) $missing[] = $name;
        }
        $report[] = $this->item(
            'Tema dosyaları',
            $missing ? 'red' : 'green',
            $missing ? ('Eksik: ' . implode(', ', $missing)) : 'Temel V10 tema dosyaları mevcut.'
        );

        $protocol = (string)$this->config->get('config_mail_protocol');
        $smtp_host = (string)$this->config->get('config_mail_smtp_hostname');
        $mail_ok = $protocol && ($protocol !== 'smtp' || $smtp_host);
        $report[] = $this->item(
            'E-posta yapılandırması',
            $mail_ok ? 'green' : 'yellow',
            $mail_ok ? ('Protokol: ' . $protocol) : 'Mail/SMTP ayarları kontrol edilmeli.'
        );

        $wa = preg_replace('/\D+/', '', (string)$this->config->get('egeser_health_whatsapp'));
        $report[] = $this->item(
            'WhatsApp',
            strlen($wa) >= 10 ? 'green' : 'yellow',
            strlen($wa) >= 10 ? ('Tanımlı: +' . $wa) : 'WhatsApp numarası tanımlı değil.'
        );

        $tracking_enabled = (bool)$this->config->get('egeser_health_tracking_status');
        $ga4 = (string)$this->config->get('egeser_health_ga4_id');
        $meta = (string)$this->config->get('egeser_health_meta_pixel_id');
        $ads = (string)$this->config->get('egeser_health_google_ads_id');
        $label = (string)$this->config->get('egeser_health_google_ads_lead_label');
        if (!$tracking_enabled) {
            $report[] = $this->item('Dönüşüm ölçümü', 'yellow', 'GA4 / Meta dönüşüm ölçümü V10 içinde hazır ancak henüz etkin değil.');
        } else {
            $ok = $this->validTrackingId($ga4, 'ga4') || $this->validTrackingId($meta, 'meta');
            $detail = 'GA4: ' . ($this->validTrackingId($ga4, 'ga4') ? 'hazır' : 'yok/geçersiz');
            $detail .= ' · Meta: ' . ($this->validTrackingId($meta, 'meta') ? 'hazır' : 'yok/geçersiz');
            if ($ads || $label) {
                $detail .= ' · Google Ads Lead: ' . (($this->validTrackingId($ads, 'ads') && $this->validTrackingId($label, 'label')) ? 'hazır' : 'eksik/geçersiz');
            }
            $report[] = $this->item('Dönüşüm ölçümü', $ok ? 'green' : 'red', $detail);
        }

        $cron_key = (string)$this->config->get('egeser_health_cron_key');
        $report[] = $this->item(
            'Günlük otomatik kontrol',
            strlen($cron_key) >= 32 ? 'green' : 'yellow',
            strlen($cron_key) >= 32 ? 'Cron güvenlik anahtarı hazır.' : 'Cron anahtarı üretilmeli.'
        );

        $pd = $this->db->query("SELECT COUNT(*) AS total,
            SUM(CASE WHEN TRIM(meta_title) = '' THEN 1 ELSE 0 END) AS no_title,
            SUM(CASE WHEN TRIM(meta_description) = '' THEN 1 ELSE 0 END) AS no_desc
            FROM `" . DB_PREFIX . "product_description`");
        $report[] = $this->item(
            'Ürün SEO meta',
            ((int)$pd->row['no_title'] || (int)$pd->row['no_desc']) ? 'yellow' : 'green',
            'Eksik title: ' . (int)$pd->row['no_title'] . ' · Eksik description: ' . (int)$pd->row['no_desc']
        );

        $cd = $this->db->query("SELECT COUNT(*) AS total,
            SUM(CASE WHEN TRIM(meta_title) = '' THEN 1 ELSE 0 END) AS no_title,
            SUM(CASE WHEN TRIM(meta_description) = '' THEN 1 ELSE 0 END) AS no_desc
            FROM `" . DB_PREFIX . "category_description`");
        $report[] = $this->item(
            'Kategori SEO meta',
            ((int)$cd->row['no_title'] || (int)$cd->row['no_desc']) ? 'yellow' : 'green',
            'Eksik title: ' . (int)$cd->row['no_title'] . ' · Eksik description: ' . (int)$cd->row['no_desc']
        );

        if ($this->tableExists('url_alias')) {
            $p = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "product` p
                WHERE p.status=1 AND NOT EXISTS (
                    SELECT 1 FROM `" . DB_PREFIX . "url_alias` u WHERE u.query=CONCAT('product_id=',p.product_id)
                )");
            $c = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "category` c
                WHERE c.status=1 AND NOT EXISTS (
                    SELECT 1 FROM `" . DB_PREFIX . "url_alias` u WHERE u.query=CONCAT('category_id=',c.category_id)
                )");
            $missing_urls = (int)$p->row['c'] + (int)$c->row['c'];
            $report[] = $this->item('SEO URL', $missing_urls ? 'yellow' : 'green', 'SEO URL eksik aktif ürün/kategori: ' . $missing_urls);
        } else {
            $report[] = $this->item('SEO URL', 'yellow', 'url_alias tablosu bulunamadı; sürüm/özelleştirme kontrolü gerekli.');
        }

        $products = $this->db->query("SELECT product_id,image FROM `" . DB_PREFIX . "product` WHERE status=1 AND image<>'' LIMIT 500");
        $missing_images = 0;
        foreach ($products->rows as $row) {
            if (!is_file(DIR_IMAGE . $row['image'])) $missing_images++;
        }
        $report[] = $this->item(
            'Ürün ana görselleri',
            $missing_images ? 'yellow' : 'green',
            'İlk 500 aktif üründe bulunamayan ana görsel: ' . $missing_images
        );

        if ($this->tableExists('egeser_lead_log')) {
            $failed = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_lead_log`
                WHERE mail_status='failed' AND date_added >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
            $report[] = $this->item(
                'Lead / mail teslim zinciri',
                (int)$failed->row['c'] ? 'red' : 'green',
                'Son 7 günde mail başarısız lead: ' . (int)$failed->row['c']
            );
        } else {
            $report[] = $this->item('Lead log tablosu', 'yellow', 'Lead yedekleme tablosu henüz kurulmamış.');
        }

        if ($this->tableExists('egeser_404_log')) {
            $q404 = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_404_log`
                WHERE last_seen >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
            $report[] = $this->item(
                '404 izleme',
                (int)$q404->row['c'] ? 'yellow' : 'green',
                'Son 7 günde farklı 404 URL: ' . (int)$q404->row['c']
            );
        } else {
            $report[] = $this->item('404 izleme', 'yellow', '404 log tablosu henüz kurulmamış.');
        }

        if ($this->tableExists('egeser_link_issue')) {
            $issues = $this->db->query("SELECT COUNT(*) AS c FROM `" . DB_PREFIX . "egeser_link_issue` WHERE is_open=1");
            $count = (int)$issues->row['c'];
            $report[] = $this->item(
                'Kırık link / görsel taraması',
                $count ? 'red' : 'green',
                'Açık kırık link/görsel sorunu: ' . $count
            );
        } else {
            $report[] = $this->item('Kırık link / görsel taraması', 'yellow', 'V10 sağlık tabloları henüz kurulmamış.');
        }

        return $report;
    }

    public function calculateScore($report) {
        if (!$report) return 0;
        $sum = 0;
        foreach ($report as $item) {
            $sum += ($item['status'] === 'green') ? 100 : (($item['status'] === 'yellow') ? 60 : 0);
        }
        return (int)round($sum / count($report));
    }

    private function baseUrl() {
        $url = (string)$this->config->get('config_ssl');
        if (!$url) $url = (string)$this->config->get('config_url');

        // Admin tarafında HTTP_SERVER / HTTPS_SERVER /admin/ adresini gösterebilir.
        // Tarayıcı daima katalog (frontend) kökünden başlamalıdır.
        if (!$url && defined('HTTPS_CATALOG')) $url = (string)HTTPS_CATALOG;
        if (!$url && defined('HTTP_CATALOG')) $url = (string)HTTP_CATALOG;

        // Catalog sabitleri yoksa ancak o zaman genel server sabitlerine düş.
        if (!$url && defined('HTTPS_SERVER')) $url = (string)HTTPS_SERVER;
        if (!$url && defined('HTTP_SERVER')) $url = (string)HTTP_SERVER;

        if (!$url && $this->registry && $this->registry->has('request')) {
            $request = $this->registry->get('request');
            if (!empty($request->server['HTTP_HOST'])) {
                $https = !empty($request->server['HTTPS']) && strtolower((string)$request->server['HTTPS']) !== 'off';
                $url = ($https ? 'https://' : 'http://') . $request->server['HTTP_HOST'] . '/';
            }
        }

        $parts = $url ? parse_url($url) : false;
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) {
            return '';
        }

        return rtrim($url, '/') . '/';
    }

    private function isAllowedScheme($url) {
        $scheme = strtolower((string)parse_url($url, PHP_URL_SCHEME));
        return in_array($scheme, array('http', 'https'), true);
    }

    private function isInternal($url) {
        if (!$this->isAllowedScheme($url)) return false;
        $base_host = strtolower((string)parse_url($this->baseUrl(), PHP_URL_HOST));
        $host = strtolower((string)parse_url($url, PHP_URL_HOST));
        return $base_host && $host && $base_host === $host;
    }

    private function normalizeUrl($url, $source) {
        $url = html_entity_decode(trim((string)$url), ENT_QUOTES, 'UTF-8');
        if ($url === '' || $url[0] === '#') return '';
        if (preg_match('#^(mailto:|tel:|javascript:|data:|whatsapp:)#i', $url)) return '';

        if (strpos($url, '//') === 0) {
            $scheme = parse_url($this->baseUrl(), PHP_URL_SCHEME);
            $url = $scheme . ':' . $url;
        } elseif (!preg_match('#^https?://#i', $url)) {
            if (substr($url, 0, 1) === '/') {
                $base = $this->baseUrl();
                $parts = $base ? parse_url($base) : false;
                if (!$parts || empty($parts['scheme']) || empty($parts['host'])) return '';
                $url = $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '') . $url;
            } else {
                $source_path = parse_url($source, PHP_URL_PATH);
                $dir = rtrim(str_replace('\\', '/', dirname($source_path ? $source_path : '/')), '/');
                $url = rtrim($this->baseUrl(), '/') . ($dir && $dir !== '.' ? $dir . '/' : '/') . $url;
            }
        }

        $parts = parse_url($url);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) return '';

        $path = isset($parts['path']) ? $parts['path'] : '/';
        $segments = array();
        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.') continue;
            if ($segment === '..') { array_pop($segments); continue; }
            $segments[] = $segment;
        }
        $path = '/' . implode('/', $segments);
        if (substr(isset($parts['path']) ? $parts['path'] : '', -1) === '/' && $path !== '/') $path .= '/';

        $query = isset($parts['query']) ? $parts['query'] : '';
        if ($query) {
            parse_str($query, $q);
            foreach (array('utm_source','utm_medium','utm_campaign','utm_term','utm_content','fbclid','gclid') as $remove) {
                unset($q[$remove]);
            }
            $query = http_build_query($q);

            // OpenCart route parametresi product/category gibi slash içerir.
            // http_build_query bunu product%2Fcategory yapar; OpenCart 2.3
            // bu biçimi route olarak çözmeyip 404 döndürebilir.
            // Yalnızca route parametresindeki encoded slash'ları geri aç.
            $query = preg_replace_callback(
                '/(^|&)route=([^&]*)/',
                function($m) {
                    return $m[1] . 'route=' . str_replace('%2F', '/', $m[2]);
                },
                $query
            );
        }

        $normalized = strtolower($parts['scheme']) . '://' . strtolower($parts['host']);
        if (isset($parts['port'])) $normalized .= ':' . (int)$parts['port'];
        $normalized .= $path;
        if ($query) $normalized .= '?' . $query;
        return $normalized;
    }

    private function shouldSkip($url) {
        $lower = strtolower($url);
        $skip = array(
            '/admin/', 'route=account/', 'route=checkout/', 'route=common/logout',
            'route=affiliate/', 'route=tool/', 'route=api/', 'route=extension/payment/',
            '/logout', '/login', '/cart', '/checkout'
        );
        foreach ($skip as $needle) {
            if (strpos($lower, $needle) !== false) return true;
        }
        return false;
    }

    private function fetch($url, $head = false) {
        if (!$this->isInternal($url) || $this->shouldSkip($url)) {
            return array('code' => 0, 'body' => '', 'type' => '', 'error' => 'blocked');
        }

        $result = array('code' => 0, 'body' => '', 'type' => '', 'error' => '');

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_USERAGENT, 'EgeserHealthBot/1.0');
            curl_setopt($ch, CURLOPT_NOBODY, $head);
            if (defined('CURLOPT_PROTOCOLS') && defined('CURLPROTO_HTTP') && defined('CURLPROTO_HTTPS')) {
                curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            }
            $body = curl_exec($ch);
            $result['code'] = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $result['type'] = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            if ($body !== false && !$head) $result['body'] = $body;
            if ($body === false) $result['error'] = curl_error($ch);
            curl_close($ch);

            if ($head && in_array($result['code'], array(0,403,405), true)) {
                return $this->fetch($url, false);
            }

            return $result;
        }

        $context = stream_context_create(array(
            'http' => array(
                'method' => $head ? 'HEAD' : 'GET',
                'timeout' => 10,
                'ignore_errors' => true,
                'header' => "User-Agent: EgeserHealthBot/1.0\r\n"
            )
        ));
        $body = @file_get_contents($url, false, $context);
        if (!$head && $body !== false) $result['body'] = $body;
        if (isset($http_response_header[0]) && preg_match('#\s([0-9]{3})\s#', $http_response_header[0], $m)) {
            $result['code'] = (int)$m[1];
        }
        if ($body === false && !$result['code']) $result['error'] = 'HTTP fetch failed';
        return $result;
    }

    private function saveIssue($source, $target, $type, $code) {
        $hash = sha1($source . '|' . $target . '|' . $type);
        $source_e = $this->db->escape(utf8_substr($source, 0, 700));
        $target_e = $this->db->escape(utf8_substr($target, 0, 700));
        $type_e = $this->db->escape(utf8_substr($type, 0, 30));
        $hash_e = $this->db->escape($hash);
        $code = (int)$code;

        $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_link_issue`
            SET issue_hash='" . $hash_e . "',
                source_url='" . $source_e . "',
                target_url='" . $target_e . "',
                issue_type='" . $type_e . "',
                http_code=" . $code . ",
                hits=1,
                first_seen=NOW(),
                last_seen=NOW(),
                is_open=1
            ON DUPLICATE KEY UPDATE
                http_code=VALUES(http_code),
                hits=hits+1,
                last_seen=NOW(),
                is_open=1");
    }

    public function scanLinks($max_pages = 40) {
        $this->installTables();
        $max_pages = max(5, min(100, (int)$max_pages));
        $max_checks = min(600, $max_pages * 12);

        // Önce eski sorunları kapalı kabul et; bu taramada tekrar bulunanlar açılır.
        $this->db->query("UPDATE `" . DB_PREFIX . "egeser_link_issue` SET is_open=0");

        $queue = array($this->baseUrl());
        $seen_pages = array();
        $checked_targets = array();
        $pages_scanned = 0;
        $links_checked = 0;
        $issues = 0;

        while ($queue && $pages_scanned < $max_pages && $links_checked < $max_checks) {
            $page = array_shift($queue);
            $page = $this->normalizeUrl($page, $this->baseUrl());
            if (!$page || isset($seen_pages[$page]) || !$this->isInternal($page) || $this->shouldSkip($page)) continue;
            $seen_pages[$page] = true;

            $response = $this->fetch($page, false);
            $pages_scanned++;

            if ($response['code'] >= 400 || $response['code'] === 0) {
                $this->saveIssue($page, $page, 'page', $response['code']);
                $issues++;
                continue;
            }

            if (stripos($response['type'], 'text/html') === false && stripos($response['body'], '<html') === false) continue;

            $html = $response['body'];
            if (!$html) continue;

            if (!class_exists('DOMDocument')) {
                continue;
            }
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            if (!@$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html)) {
                libxml_clear_errors();
                continue;
            }
            libxml_clear_errors();

            $targets = array();
            foreach ($dom->getElementsByTagName('a') as $node) {
                if ($node->hasAttribute('href')) {
                    $u = $this->normalizeUrl($node->getAttribute('href'), $page);
                    if ($u && $this->isInternal($u) && !$this->shouldSkip($u)) $targets[] = array($u, 'link');
                }
            }
            foreach ($dom->getElementsByTagName('img') as $node) {
                if ($node->hasAttribute('src')) {
                    $u = $this->normalizeUrl($node->getAttribute('src'), $page);
                    if ($u && $this->isInternal($u)) $targets[] = array($u, 'image');
                }
            }

            foreach ($targets as $targetInfo) {
                if ($links_checked >= $max_checks) break;
                list($target, $type) = $targetInfo;
                $key = $type . '|' . $target;
                if (isset($checked_targets[$key])) {
                    if ($type === 'link' && !isset($seen_pages[$target]) && count($queue) < $max_pages * 3) $queue[] = $target;
                    continue;
                }
                $checked_targets[$key] = true;
                $links_checked++;

                $r = $this->fetch($target, $type === 'image');
                if ($r['code'] >= 400 || $r['code'] === 0) {
                    $this->saveIssue($page, $target, $type, $r['code']);
                    $issues++;
                } elseif ($type === 'link' && $r['code'] >= 200 && $r['code'] < 400 && !isset($seen_pages[$target]) && count($queue) < $max_pages * 3) {
                    $queue[] = $target;
                }
            }
        }

        return array(
            'pages_scanned' => $pages_scanned,
            'links_checked' => $links_checked,
            'issues_found' => $issues,
            'scan_valid' => ($pages_scanned > 0)
        );
    }

    public function run($run_type = 'manual', $with_scan = false, $max_pages = 40) {
        $this->installTables();

        $run_type = preg_replace('/[^a-z0-9_-]/i', '', (string)$run_type);
        if (!$run_type) $run_type = 'manual';

        $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_health_run`
            SET run_type='" . $this->db->escape($run_type) . "',
                status='running',
                date_started=NOW(),
                details='{}'");
        $run_id = (int)$this->db->getLastId();

        $scan = array('pages_scanned'=>0, 'links_checked'=>0, 'issues_found'=>0);
        if ($with_scan) {
            $scan = $this->scanLinks($max_pages);
        }

        $report = $this->getReport();
        $score = $this->calculateScore($report);
        $status = $score >= 85 ? 'green' : ($score >= 65 ? 'yellow' : 'red');

        $details = json_encode(array('report'=>$report, 'scan'=>$scan), JSON_UNESCAPED_UNICODE);
        if ($details === false) $details = '{}';

        $this->db->query("UPDATE `" . DB_PREFIX . "egeser_health_run`
            SET status='" . $this->db->escape($status) . "',
                score=" . (int)$score . ",
                pages_scanned=" . (int)$scan['pages_scanned'] . ",
                links_checked=" . (int)$scan['links_checked'] . ",
                issues_found=" . (int)$scan['issues_found'] . ",
                details='" . $this->db->escape($details) . "',
                date_finished=NOW()
            WHERE run_id=" . $run_id);

        return array(
            'run_id'=>$run_id,
            'status'=>$status,
            'score'=>$score,
            'report'=>$report,
            'scan'=>$scan
        );
    }

    public function getRecentRuns($limit = 10) {
        if (!$this->tableExists('egeser_health_run')) return array();
        $limit = max(1, min(50, (int)$limit));
        return $this->db->query("SELECT run_id,run_type,status,score,pages_scanned,links_checked,issues_found,date_started,date_finished
            FROM `" . DB_PREFIX . "egeser_health_run`
            ORDER BY run_id DESC LIMIT " . $limit)->rows;
    }

    public function getOpenIssues($limit = 25) {
        if (!$this->tableExists('egeser_link_issue')) return array();
        $limit = max(1, min(100, (int)$limit));
        return $this->db->query("SELECT issue_id,source_url,target_url,issue_type,http_code,hits,last_seen
            FROM `" . DB_PREFIX . "egeser_link_issue`
            WHERE is_open=1 ORDER BY last_seen DESC LIMIT " . $limit)->rows;
    }

    public function cleanup($days) {
        $days = max(30, min(730, (int)$days));
        if ($this->tableExists('egeser_health_run')) {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_health_run`
                WHERE date_started < DATE_SUB(NOW(), INTERVAL " . $days . " DAY)");
        }
        if ($this->tableExists('egeser_link_issue')) {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_link_issue`
                WHERE is_open=0 AND last_seen < DATE_SUB(NOW(), INTERVAL 180 DAY)");
        }
    }
}
