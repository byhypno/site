<?php
/**
 * Egeser Performance Monitor
 * OpenCart 2.x compatible.
 *
 * Ölçümler laboratuvar tipi sunucu/HTML kontrolleridir.
 * Gerçek kullanıcı Core Web Vitals (LCP/INP/CLS) için GA4 / CrUX /
 * PageSpeed Insights doğrulaması ayrıca yapılmalıdır.
 */
class EgeserPerformanceMonitor {
    private $registry;
    private $db;
    private $config;

    public function __construct($registry) {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->config = $registry->get('config');
    }

    public function installTables() {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "egeser_performance_run` (
            `performance_id` INT(11) NOT NULL AUTO_INCREMENT,
            `run_type` VARCHAR(20) NOT NULL DEFAULT 'manual',
            `status` VARCHAR(20) NOT NULL DEFAULT 'running',
            `score` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
            `pages_tested` INT(11) NOT NULL DEFAULT 0,
            `avg_response_ms` INT(11) NOT NULL DEFAULT 0,
            `avg_html_kb` INT(11) NOT NULL DEFAULT 0,
            `issues_found` INT(11) NOT NULL DEFAULT 0,
            `details` MEDIUMTEXT NOT NULL,
            `date_started` DATETIME NOT NULL,
            `date_finished` DATETIME NULL,
            PRIMARY KEY (`performance_id`),
            KEY `idx_perf_date` (`date_started`),
            KEY `idx_perf_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
    }

    private function baseUrl() {
        $url = (string)$this->config->get('config_ssl');
        if (!$url) $url = (string)$this->config->get('config_url');

        // Fresh OpenCart 2.3 installs may have empty config_url/config_ssl.
        // Fall back to catalog constants and then the active HTTP host.
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

    private function sameHost($url) {
        $base = $this->baseUrl();
        if (!$base || !$url) return false;

        $base_host = strtolower((string)parse_url($base, PHP_URL_HOST));
        $host = strtolower((string)parse_url($url, PHP_URL_HOST));
        return $base_host && $host && $base_host === $host;
    }

    private function fetch($url) {
        $result = array(
            'code'=>0,
            'body'=>'',
            'content_type'=>'',
            'time_ms'=>0,
            'bytes'=>0,
            'headers'=>array(),
            'error'=>''
        );

        if (!$this->sameHost($url)) {
            $result['error'] = 'cross-host blocked';
            return $result;
        }

        if (function_exists('curl_init')) {
            $headers = array();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 12);
            curl_setopt($ch, CURLOPT_USERAGENT, 'EgeserPerformanceBot/1.0');
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $line) use (&$headers) {
                $len = strlen($line);
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $headers[strtolower(trim($parts[0]))] = trim($parts[1]);
                }
                return $len;
            });
            if (defined('CURLOPT_PROTOCOLS') && defined('CURLPROTO_HTTP') && defined('CURLPROTO_HTTPS')) {
                curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            }
            $body = curl_exec($ch);
            $result['code'] = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $result['content_type'] = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            $result['time_ms'] = (int)round(((float)curl_getinfo($ch, CURLINFO_TOTAL_TIME)) * 1000);
            if ($body !== false) {
                $result['body'] = $body;
                $result['bytes'] = strlen($body);
            } else {
                $result['error'] = curl_error($ch);
            }
            $result['headers'] = $headers;
            curl_close($ch);
            return $result;
        }

        $start = microtime(true);
        $context = stream_context_create(array(
            'http'=>array(
                'method'=>'GET',
                'timeout'=>12,
                'ignore_errors'=>true,
                'header'=>"User-Agent: EgeserPerformanceBot/1.0\r\nAccept-Encoding: gzip\r\n"
            )
        ));
        $body = @file_get_contents($url, false, $context);
        $result['time_ms'] = (int)round((microtime(true)-$start)*1000);
        if ($body !== false) {
            $result['body'] = $body;
            $result['bytes'] = strlen($body);
        }
        if (isset($http_response_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $line) {
                if (preg_match('#^HTTP/\S+\s+([0-9]{3})#i', $line, $m)) {
                    $result['code'] = (int)$m[1];
                } elseif (strpos($line, ':') !== false) {
                    list($k,$v)=explode(':',$line,2);
                    $result['headers'][strtolower(trim($k))]=trim($v);
                }
            }
        }
        if ($body === false && !$result['code']) $result['error']='HTTP fetch failed';
        return $result;
    }

    private function getSampleUrls() {
        $base = $this->baseUrl();
        if (!$base) return array();

        $urls = array(array('type'=>'home','url'=>$base));

        $cat = $this->db->query("SELECT category_id FROM `" . DB_PREFIX . "category` WHERE status=1 ORDER BY sort_order, category_id LIMIT 1");
        if ($cat->num_rows) {
            $urls[] = array(
                'type'=>'category',
                'url'=>$base . 'index.php?route=product/category&path=' . (int)$cat->row['category_id']
            );
        }

        $product = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE status=1 ORDER BY product_id DESC LIMIT 1");
        if ($product->num_rows) {
            $urls[] = array(
                'type'=>'product',
                'url'=>$base . 'index.php?route=product/product&product_id=' . (int)$product->row['product_id']
            );
        }

        return $urls;
    }

    private function analyzeHtml($html, $url) {
        $metrics = array(
            'images'=>0,
            'images_missing_dimensions'=>0,
            'below_fold_images_without_lazy'=>0,
            'scripts'=>0,
            'blocking_scripts'=>0,
            'stylesheets'=>0,
            'inline_style_blocks'=>0,
            'third_party_origins'=>array(),
            'h1_count'=>0,
            'dom_nodes'=>0,
            'hero_priority'=>false
        );

        if (!$html) return $metrics;

        if (!class_exists('DOMDocument')) return $metrics;
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        if (!@$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html)) {
            libxml_clear_errors();
            return $metrics;
        }
        libxml_clear_errors();

        $all = $dom->getElementsByTagName('*');
        $metrics['dom_nodes'] = $all->length;
        $metrics['h1_count'] = $dom->getElementsByTagName('h1')->length;

        $base_host = strtolower((string)parse_url($url, PHP_URL_HOST));

        foreach ($dom->getElementsByTagName('img') as $img) {
            $metrics['images']++;
            if (!$img->hasAttribute('width') || !$img->hasAttribute('height')) {
                $metrics['images_missing_dimensions']++;
            }
            if ($metrics['images'] > 2 && strtolower($img->getAttribute('loading')) !== 'lazy') {
                $metrics['below_fold_images_without_lazy']++;
            }
            if (strtolower($img->getAttribute('fetchpriority')) === 'high') {
                $metrics['hero_priority'] = true;
            }
            $src = trim($img->getAttribute('src'));
            if (preg_match('#^https?://#i', $src)) {
                $host = strtolower((string)parse_url($src, PHP_URL_HOST));
                if ($host && $host !== $base_host) $metrics['third_party_origins'][$host]=true;
            }
        }

        foreach ($dom->getElementsByTagName('script') as $script) {
            $metrics['scripts']++;
            $src = trim($script->getAttribute('src'));
            if ($src && !$script->hasAttribute('defer') && !$script->hasAttribute('async') && !$script->hasAttribute('type')) {
                $metrics['blocking_scripts']++;
            }
            if (preg_match('#^https?://#i', $src)) {
                $host = strtolower((string)parse_url($src, PHP_URL_HOST));
                if ($host && $host !== $base_host) $metrics['third_party_origins'][$host]=true;
            }
        }

        foreach ($dom->getElementsByTagName('link') as $link) {
            if (strtolower($link->getAttribute('rel')) === 'stylesheet') {
                $metrics['stylesheets']++;
            }
            $href = trim($link->getAttribute('href'));
            if (preg_match('#^https?://#i', $href)) {
                $host = strtolower((string)parse_url($href, PHP_URL_HOST));
                if ($host && $host !== $base_host) $metrics['third_party_origins'][$host]=true;
            }
        }

        $metrics['inline_style_blocks'] = $dom->getElementsByTagName('style')->length;
        $metrics['third_party_origins'] = array_keys($metrics['third_party_origins']);
        return $metrics;
    }

    private function scorePage($response, $metrics) {
        $score = 100;
        $issues = array();

        if ($response['code'] < 200 || $response['code'] >= 400) {
            $score -= 50; $issues[]='HTTP yanıtı başarısız: ' . (int)$response['code'];
        }

        if ($response['time_ms'] > 1800) {
            $score -= 20; $issues[]='Sunucu/HTML yanıtı yavaş (>1800 ms)';
        } elseif ($response['time_ms'] > 900) {
            $score -= 10; $issues[]='Sunucu/HTML yanıtı geliştirilebilir (>900 ms)';
        }

        $html_kb = (int)round($response['bytes']/1024);
        if ($html_kb > 250) {
            $score -= 12; $issues[]='HTML boyutu yüksek (>250 KB)';
        } elseif ($html_kb > 150) {
            $score -= 6; $issues[]='HTML boyutu geliştirilebilir (>150 KB)';
        }

        if ($metrics['images_missing_dimensions'] > 0) {
            $score -= min(15, $metrics['images_missing_dimensions'] * 3);
            $issues[]='Boyut belirtilmemiş görsel: ' . $metrics['images_missing_dimensions'];
        }

        if ($metrics['below_fold_images_without_lazy'] > 2) {
            $score -= min(10, $metrics['below_fold_images_without_lazy']);
            $issues[]='Lazy-load adayı görsel: ' . $metrics['below_fold_images_without_lazy'];
        }

        if ($metrics['blocking_scripts'] > 2) {
            $score -= min(15, ($metrics['blocking_scripts'] - 2) * 3);
            $issues[]='Render-blocking script adayı: ' . $metrics['blocking_scripts'];
        }

        if ($metrics['dom_nodes'] > 1800) {
            $score -= 10; $issues[]='DOM çok büyük (>1800 node)';
        } elseif ($metrics['dom_nodes'] > 1200) {
            $score -= 5; $issues[]='DOM boyutu yüksek (>1200 node)';
        }

        if (count($metrics['third_party_origins']) > 5) {
            $score -= 8; $issues[]='Çok sayıda üçüncü taraf origin: ' . count($metrics['third_party_origins']);
        }

        if ($metrics['h1_count'] !== 1) {
            $score -= 5; $issues[]='H1 sayısı 1 değil: ' . $metrics['h1_count'];
        }

        return array(
            'score'=>max(0,(int)$score),
            'issues'=>$issues
        );
    }

    public function run($run_type='manual') {
        $this->installTables();
        $run_type = preg_replace('/[^a-z0-9_-]/i','',(string)$run_type);
        if (!$run_type) $run_type='manual';

        $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_performance_run`
            SET run_type='" . $this->db->escape($run_type) . "',
                status='running', details='{}', date_started=NOW()");
        $id=(int)$this->db->getLastId();

        $pages=array();
        $scores=array();
        $times=array();
        $sizes=array();
        $issue_count=0;

        foreach ($this->getSampleUrls() as $sample) {
            $response=$this->fetch($sample['url']);
            $metrics=$this->analyzeHtml($response['body'],$sample['url']);
            $graded=$this->scorePage($response,$metrics);

            $html_kb=(int)round($response['bytes']/1024);
            $pages[]=array(
                'type'=>$sample['type'],
                'url'=>$sample['url'],
                'http_code'=>$response['code'],
                'response_ms'=>$response['time_ms'],
                'html_kb'=>$html_kb,
                'metrics'=>$metrics,
                'score'=>$graded['score'],
                'issues'=>$graded['issues'],
                'cache_control'=>isset($response['headers']['cache-control']) ? $response['headers']['cache-control'] : '',
                'content_encoding'=>isset($response['headers']['content-encoding']) ? $response['headers']['content-encoding'] : ''
            );
            $scores[]=$graded['score'];
            $times[]=$response['time_ms'];
            $sizes[]=$html_kb;
            $issue_count+=count($graded['issues']);
        }

        $score=$scores ? (int)round(array_sum($scores)/count($scores)) : 0;
        $avg_time=$times ? (int)round(array_sum($times)/count($times)) : 0;
        $avg_size=$sizes ? (int)round(array_sum($sizes)/count($sizes)) : 0;
        $status=$score>=85?'green':($score>=65?'yellow':'red');

        $details=json_encode(array('pages'=>$pages),JSON_UNESCAPED_UNICODE);
        if ($details===false) $details='{}';

        $this->db->query("UPDATE `" . DB_PREFIX . "egeser_performance_run`
            SET status='" . $this->db->escape($status) . "',
                score=" . (int)$score . ",
                pages_tested=" . count($pages) . ",
                avg_response_ms=" . $avg_time . ",
                avg_html_kb=" . $avg_size . ",
                issues_found=" . (int)$issue_count . ",
                details='" . $this->db->escape($details) . "',
                date_finished=NOW()
            WHERE performance_id=" . $id);

        return array(
            'performance_id'=>$id,
            'status'=>$status,
            'score'=>$score,
            'pages_tested'=>count($pages),
            'avg_response_ms'=>$avg_time,
            'avg_html_kb'=>$avg_size,
            'issues_found'=>$issue_count,
            'pages'=>$pages
        );
    }

    public function getRecentRuns($limit=10) {
        $this->installTables();
        $limit=max(1,min(50,(int)$limit));
        return $this->db->query("SELECT performance_id,run_type,status,score,pages_tested,avg_response_ms,avg_html_kb,issues_found,date_started,date_finished
            FROM `" . DB_PREFIX . "egeser_performance_run`
            ORDER BY performance_id DESC LIMIT " . $limit)->rows;
    }

    public function getLatest() {
        $runs=$this->getRecentRuns(1);
        return $runs ? $runs[0] : array();
    }

    public function cleanup($days) {
        $this->installTables();
        $days=max(30,min(730,(int)$days));
        $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_performance_run`
            WHERE date_started < DATE_SUB(NOW(), INTERVAL " . $days . " DAY)");
    }
}
