<?php
require_once(DIR_SYSTEM . 'library/egeser_visitor_schema.php');

/**
 * Egeser Ziyaretci & Lead Takip Merkezi - birinci taraf ziyaretci/oturum
 * kimligi ve sayfa goruntuleme kaydi.
 *
 * Tasarim kurallari (bkz. ilk analiz onayi):
 *  - Ham IP hicbir tabloda saklanmaz.
 *  - visitor/session cookie degerleri tahmin edilemez random token'dir.
 *  - Botlar tespit edilirse hicbir kayit yazilmaz (KPI kirlenmesin diye).
 *  - Tek sayfa yuklemesinde en fazla 1 event yazilir (page_view/product_view/
 *    category_view birbirinin yerine gecer, ust uste eklenmez).
 */
class EgeserVisitorTracker {
    const COOKIE_VISITOR = 'egs_vid';
    const COOKIE_SESSION = 'egs_sid';
    const VISITOR_TTL = 34560000; // 400 gun (tarayici cerez sinirlarina uygun ust sinir)
    const SESSION_TTL = 1800;     // 30 dakika hareketsizlikte oturum sona erer
    const DEDUPE_WINDOW = 3;      // ayni route icin saniye bazli tekrar filtresi

    private $registry;
    private $db;
    private $request;
    private $config;

    public function __construct($registry) {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->request = $registry->get('request');
        $this->config = $registry->get('config');
    }

    /**
     * Sayfa yuklemesi basina bir kez cagrilir (common/header controller'indan).
     * Donen dizi: array('visitor_id'=>int,'session_id'=>int) ya da bot/hata
     * durumunda null.
     */
    public function trackPageView($page_title = '') {
        if ($this->isBot()) {
            return null;
        }

        try {
            EgeserVisitorSchema::install($this->db);

            $visitor_id = $this->resolveVisitor();
            $session = $this->resolveSession($visitor_id);

            $route = isset($this->request->get['route']) ? (string)$this->request->get['route'] : '';
            list($event_type, $entity_type, $entity_id) = $this->classifyRoute($route);

            $page_url = $this->currentUrl();

            if (!$this->isDuplicate($session['session_id'], $route)) {
                $this->insertEvent($visitor_id, $session['session_id'], $event_type, array(
                    'page_url' => $page_url,
                    'page_title' => utf8_substr((string)$page_title, 0, 255),
                    'route' => utf8_substr($route, 0, 150),
                    'entity_type' => $entity_type,
                    'entity_id' => $entity_id,
                    'referrer' => $this->refererHost() !== '' ? utf8_substr($this->referer(), 0, 700) : ''
                ));

                $this->db->query("UPDATE `" . DB_PREFIX . "egeser_session` SET pageviews=pageviews+1, last_activity=NOW() WHERE session_id=" . (int)$session['session_id']);
                $this->db->query("UPDATE `" . DB_PREFIX . "egeser_visitor` SET last_seen=NOW() WHERE visitor_id=" . (int)$visitor_id);
            }

            return array('visitor_id' => $visitor_id, 'session_id' => $session['session_id']);
        } catch (Exception $e) {
            $this->logError('trackPageView', $e);
            return null;
        }
    }

    /**
     * Frontend beacon endpoint'inden gelen tikanma/form event'leri icin.
     * Cookie yoksa sessizce false doner (sayfa ziyareti hic gerceklesmemis
     * demektir, spam/bot ihtimali yuksektir).
     */
    public function trackClientEvent($event_type, array $data = array()) {
        if ($this->isBot()) {
            return false;
        }

        $allowed = array(
            'whatsapp_click', 'phone_click', 'quote_form_start', 'quote_form_submit',
            'contact_form_submit', 'map_click', 'brochure_click'
        );
        if (!in_array($event_type, $allowed, true)) {
            return false;
        }

        $visitor_token = $this->cookie(self::COOKIE_VISITOR);
        $session_token = $this->cookie(self::COOKIE_SESSION);
        if (!$visitor_token || !$session_token) {
            return false;
        }

        try {
            EgeserVisitorSchema::install($this->db);

            $visitor_id = $this->findVisitorByToken($visitor_token);
            $session = $this->findSessionByToken($session_token);
            if (!$visitor_id || !$session) {
                return false;
            }

            $this->insertEvent($visitor_id, $session['session_id'], $event_type, array(
                'page_url' => isset($data['page_url']) ? utf8_substr((string)$data['page_url'], 0, 700) : '',
                'entity_type' => isset($data['entity_type']) ? utf8_substr((string)$data['entity_type'], 0, 30) : '',
                'entity_id' => isset($data['entity_id']) ? (int)$data['entity_id'] : 0,
                'event_value' => isset($data['placement']) ? utf8_substr((string)$data['placement'], 0, 255) : ''
            ));

            $this->db->query("UPDATE `" . DB_PREFIX . "egeser_session` SET last_activity=NOW() WHERE session_id=" . (int)$session['session_id']);

            return true;
        } catch (Exception $e) {
            $this->logError('trackClientEvent', $e);
            return false;
        }
    }

    /**
     * Teklif/iletisim formu basariyla kaydedildiginde lead ile
     * visitor/session eslestirmesi icin cagrilir.
     */
    public function attachLead($lead_id) {
        if ($this->isBot()) {
            return;
        }

        $session_token = $this->cookie(self::COOKIE_SESSION);
        if (!$session_token) {
            return;
        }

        try {
            EgeserVisitorSchema::install($this->db);
            $session = $this->findSessionByToken($session_token);
            if (!$session) {
                return;
            }

            $this->db->query("UPDATE `" . DB_PREFIX . "egeser_session`
                SET converted=1, lead_id=" . (int)$lead_id . "
                WHERE session_id=" . (int)$session['session_id']);

            $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_conversion`
                SET visitor_id=" . (int)$session['visitor_id'] . ",
                    session_id=" . (int)$session['session_id'] . ",
                    lead_id=" . (int)$lead_id . ",
                    conversion_type='lead',
                    source='" . $this->db->escape(utf8_substr((string)$session['source'], 0, 60)) . "',
                    medium='" . $this->db->escape(utf8_substr((string)$session['medium'], 0, 60)) . "',
                    campaign='" . $this->db->escape(utf8_substr((string)$session['campaign'], 0, 150)) . "',
                    created_at=NOW()");
        } catch (Exception $e) {
            $this->logError('attachLead', $e);
        }
    }

    // ------------------------------------------------------------------
    // Visitor / session resolution
    // ------------------------------------------------------------------

    private function resolveVisitor() {
        $token = $this->cookie(self::COOKIE_VISITOR);
        $visitor_id = $token ? $this->findVisitorByToken($token) : null;

        if ($visitor_id) {
            return $visitor_id;
        }

        $token = $this->generateToken();
        $touch = $this->currentTouch();
        $device = $this->parseDevice();

        $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_visitor`
            SET visitor_token='" . $this->db->escape($token) . "',
                first_seen=NOW(), last_seen=NOW(),
                first_source='" . $this->db->escape($touch['source']) . "',
                first_medium='" . $this->db->escape($touch['medium']) . "',
                first_campaign='" . $this->db->escape($touch['campaign']) . "',
                first_referrer='" . $this->db->escape(utf8_substr($touch['referrer'], 0, 700)) . "',
                first_landing_page='" . $this->db->escape(utf8_substr($this->currentUrl(), 0, 700)) . "',
                device_type='" . $this->db->escape($device['device_type']) . "',
                browser='" . $this->db->escape($device['browser']) . "',
                os='" . $this->db->escape($device['os']) . "',
                is_bot=0,
                created_at=NOW(), updated_at=NOW()");

        $visitor_id = (int)$this->db->getLastId();
        $this->setCookie(self::COOKIE_VISITOR, $token, self::VISITOR_TTL);

        return $visitor_id;
    }

    private function resolveSession($visitor_id) {
        $token = $this->cookie(self::COOKIE_SESSION);
        $session = $token ? $this->findSessionByToken($token) : null;

        if ($session) {
            $this->setCookie(self::COOKIE_SESSION, $token, self::SESSION_TTL);
            return $session;
        }

        $token = $this->generateToken();
        $touch = $this->currentTouch();
        $device = $this->parseDevice();

        $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_session`
            SET session_token='" . $this->db->escape($token) . "',
                visitor_id=" . (int)$visitor_id . ",
                started_at=NOW(), last_activity=NOW(),
                source='" . $this->db->escape($touch['source']) . "',
                medium='" . $this->db->escape($touch['medium']) . "',
                campaign='" . $this->db->escape($touch['campaign']) . "',
                term='" . $this->db->escape($touch['term']) . "',
                content='" . $this->db->escape($touch['content']) . "',
                referrer='" . $this->db->escape(utf8_substr($touch['referrer'], 0, 700)) . "',
                landing_page='" . $this->db->escape(utf8_substr($this->currentUrl(), 0, 700)) . "',
                gclid='" . $this->db->escape($touch['gclid']) . "',
                fbclid='" . $this->db->escape($touch['fbclid']) . "',
                device_type='" . $this->db->escape($device['device_type']) . "',
                browser='" . $this->db->escape($device['browser']) . "',
                os='" . $this->db->escape($device['os']) . "',
                pageviews=0, converted=0, lead_id=0,
                created_at=NOW()");

        $session_id = (int)$this->db->getLastId();
        $this->setCookie(self::COOKIE_SESSION, $token, self::SESSION_TTL);

        return array(
            'session_id' => $session_id,
            'visitor_id' => $visitor_id,
            'source' => $touch['source'],
            'medium' => $touch['medium'],
            'campaign' => $touch['campaign']
        );
    }

    private function findVisitorByToken($token) {
        $token = preg_replace('/[^a-f0-9]/', '', (string)$token);
        if (strlen($token) !== 48) {
            return null;
        }

        $q = $this->db->query("SELECT visitor_id FROM `" . DB_PREFIX . "egeser_visitor` WHERE visitor_token='" . $this->db->escape($token) . "' LIMIT 1");
        return $q->num_rows ? (int)$q->row['visitor_id'] : null;
    }

    private function findSessionByToken($token) {
        $token = preg_replace('/[^a-f0-9]/', '', (string)$token);
        if (strlen($token) !== 48) {
            return null;
        }

        $q = $this->db->query("SELECT session_id, visitor_id, source, medium, campaign, last_activity
            FROM `" . DB_PREFIX . "egeser_session`
            WHERE session_token='" . $this->db->escape($token) . "' LIMIT 1");

        if (!$q->num_rows) {
            return null;
        }

        if ((time() - strtotime($q->row['last_activity'])) > self::SESSION_TTL) {
            return null; // oturum suresi dolmus, yeni oturum acilacak
        }

        return $q->row;
    }

    private function isDuplicate($session_id, $route) {
        $q = $this->db->query("SELECT event_id FROM `" . DB_PREFIX . "egeser_event`
            WHERE session_id=" . (int)$session_id . "
              AND route='" . $this->db->escape(utf8_substr($route, 0, 150)) . "'
              AND created_at >= DATE_SUB(NOW(), INTERVAL " . (int)self::DEDUPE_WINDOW . " SECOND)
            ORDER BY event_id DESC LIMIT 1");

        return (bool)$q->num_rows;
    }

    private function insertEvent($visitor_id, $session_id, $event_type, array $data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_event`
            SET visitor_id=" . (int)$visitor_id . ",
                session_id=" . (int)$session_id . ",
                event_type='" . $this->db->escape($event_type) . "',
                page_url='" . $this->db->escape(isset($data['page_url']) ? $data['page_url'] : '') . "',
                page_title='" . $this->db->escape(isset($data['page_title']) ? $data['page_title'] : '') . "',
                route='" . $this->db->escape(isset($data['route']) ? $data['route'] : '') . "',
                entity_type='" . $this->db->escape(isset($data['entity_type']) ? $data['entity_type'] : '') . "',
                entity_id=" . (isset($data['entity_id']) ? (int)$data['entity_id'] : 0) . ",
                entity_name='',
                referrer='" . $this->db->escape(isset($data['referrer']) ? $data['referrer'] : '') . "',
                event_value='" . $this->db->escape(isset($data['event_value']) ? $data['event_value'] : '') . "',
                metadata_json='',
                created_at=NOW()");
    }

    // ------------------------------------------------------------------
    // Route -> event_type siniflandirmasi
    // ------------------------------------------------------------------

    private function classifyRoute($route) {
        if ($route === 'product/product') {
            $id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
            return array('product_view', 'product', $id);
        }

        if ($route === 'product/category') {
            $path = isset($this->request->get['path']) ? (string)$this->request->get['path'] : '';
            $parts = explode('_', $path);
            $id = (int)end($parts);
            return array('category_view', 'category', $id);
        }

        return array('page_view', '', 0);
    }

    // ------------------------------------------------------------------
    // Trafik kaynagi siniflandirmasi (first-touch veya current-session)
    // ------------------------------------------------------------------

    private function currentTouch() {
        $get = isset($this->request->get) ? $this->request->get : array();

        $utm_source = isset($get['utm_source']) ? utf8_substr((string)$get['utm_source'], 0, 60) : '';
        $utm_medium = isset($get['utm_medium']) ? utf8_substr((string)$get['utm_medium'], 0, 60) : '';
        $utm_campaign = isset($get['utm_campaign']) ? utf8_substr((string)$get['utm_campaign'], 0, 150) : '';
        $utm_term = isset($get['utm_term']) ? utf8_substr((string)$get['utm_term'], 0, 150) : '';
        $utm_content = isset($get['utm_content']) ? utf8_substr((string)$get['utm_content'], 0, 150) : '';
        $gclid = isset($get['gclid']) ? utf8_substr((string)$get['gclid'], 0, 150) : (isset($get['gbraid']) ? utf8_substr((string)$get['gbraid'], 0, 150) : (isset($get['wbraid']) ? utf8_substr((string)$get['wbraid'], 0, 150) : ''));
        $fbclid = isset($get['fbclid']) ? utf8_substr((string)$get['fbclid'], 0, 150) : '';
        $referrer = $this->referer();

        if ($utm_source !== '' || $utm_medium !== '') {
            $source = $utm_source !== '' ? $utm_source : 'other';
            $medium = $utm_medium !== '' ? $utm_medium : 'referral';
        } elseif ($gclid !== '') {
            $source = 'google';
            $medium = 'cpc';
        } elseif ($fbclid !== '') {
            $source = 'facebook';
            $medium = 'cpc';
        } else {
            $host = $this->refererHost();
            $self_host = strtolower((string)parse_url($this->config->get('config_url'), PHP_URL_HOST));

            if ($host === '' || $host === $self_host) {
                $source = 'direct';
                $medium = 'none';
            } elseif (preg_match('/(^|\.)google\./i', $host)) {
                $source = 'google';
                $medium = 'organic';
            } elseif (preg_match('/(^|\.)(instagram)\.com$/i', $host)) {
                $source = 'instagram';
                $medium = 'organic';
            } elseif (preg_match('/(^|\.)(facebook|fb)\.com$/i', $host)) {
                $source = 'facebook';
                $medium = 'organic';
            } else {
                $source = $host;
                $medium = 'referral';
            }
        }

        return array(
            'source' => $source, 'medium' => $medium, 'campaign' => $utm_campaign,
            'term' => $utm_term, 'content' => $utm_content,
            'gclid' => $gclid, 'fbclid' => $fbclid, 'referrer' => $referrer
        );
    }

    // ------------------------------------------------------------------
    // Cihaz / tarayici / OS (hafif regex tabanli, harici kutuphane yok)
    // ------------------------------------------------------------------

    private function parseDevice() {
        $ua = isset($this->request->server['HTTP_USER_AGENT']) ? (string)$this->request->server['HTTP_USER_AGENT'] : '';

        if (preg_match('/ipad|tablet/i', $ua) && !preg_match('/mobile/i', $ua)) {
            $device_type = 'tablet';
        } elseif (preg_match('/mobi|android|iphone/i', $ua)) {
            $device_type = 'mobile';
        } else {
            $device_type = 'desktop';
        }

        if (preg_match('/edg\//i', $ua)) {
            $browser = 'Edge';
        } elseif (preg_match('/opr\/|opera/i', $ua)) {
            $browser = 'Opera';
        } elseif (preg_match('/chrome|crios/i', $ua)) {
            $browser = 'Chrome';
        } elseif (preg_match('/firefox|fxios/i', $ua)) {
            $browser = 'Firefox';
        } elseif (preg_match('/safari/i', $ua)) {
            $browser = 'Safari';
        } else {
            $browser = 'Other';
        }

        if (preg_match('/windows/i', $ua)) {
            $os = 'Windows';
        } elseif (preg_match('/android/i', $ua)) {
            $os = 'Android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $ua)) {
            $os = 'iOS';
        } elseif (preg_match('/mac os x|macintosh/i', $ua)) {
            $os = 'macOS';
        } elseif (preg_match('/linux/i', $ua)) {
            $os = 'Linux';
        } else {
            $os = 'Other';
        }

        return array('device_type' => $device_type, 'browser' => $browser, 'os' => $os);
    }

    // ------------------------------------------------------------------
    // Bot filtreleme
    // ------------------------------------------------------------------

    private function isBot() {
        $ua = isset($this->request->server['HTTP_USER_AGENT']) ? (string)$this->request->server['HTTP_USER_AGENT'] : '';
        if ($ua === '') {
            return true;
        }

        $patterns = '/googlebot|bingbot|yandexbot|baiduspider|duckduckbot|ahrefsbot|semrushbot|mj12bot|dotbot|petalbot|bytespider|applebot|facebookexternalhit|slackbot|telegrambot|whatsapp|discordbot|linkedinbot|pinterestbot|redditbot|curl|wget|python-requests|python-urllib|scrapy|headlesschrome|phantomjs|selenium|puppeteer|bot|spider|crawler|crawling|monitor|uptime|pingdom|statuscake/i';

        return (bool)preg_match($patterns, $ua);
    }

    // ------------------------------------------------------------------
    // Yardimcilar
    // ------------------------------------------------------------------

    private function generateToken() {
        return bin2hex(random_bytes(24));
    }

    private function cookie($name) {
        if (isset($this->request->cookie[$name])) {
            return (string)$this->request->cookie[$name];
        }

        return isset($_COOKIE[$name]) ? (string)$_COOKIE[$name] : '';
    }

    private function setCookie($name, $value, $ttl) {
        if (headers_sent()) {
            return;
        }

        $https = !empty($this->request->server['HTTPS']) && $this->request->server['HTTPS'] !== 'off';
        $expires = time() + $ttl;

        if (PHP_VERSION_ID >= 70300) {
            setcookie($name, $value, array(
                'expires' => $expires,
                'path' => '/',
                'secure' => $https,
                'httponly' => true,
                'samesite' => 'Lax'
            ));
        } else {
            // PHP < 7.3: setcookie() has no SameSite parameter; append it to
            // the path, a well-known compatible workaround for older PHP.
            setcookie($name, $value, $expires, '/; SameSite=Lax', '', $https, true);
        }

        $_COOKIE[$name] = $value;
    }

    private function currentUrl() {
        $https = !empty($this->request->server['HTTPS']) && $this->request->server['HTTPS'] !== 'off';
        $host = isset($this->request->server['HTTP_HOST']) ? $this->request->server['HTTP_HOST'] : '';
        $uri = isset($this->request->server['REQUEST_URI']) ? $this->request->server['REQUEST_URI'] : '';

        return ($https ? 'https://' : 'http://') . $host . $uri;
    }

    private function referer() {
        return isset($this->request->server['HTTP_REFERER']) ? (string)$this->request->server['HTTP_REFERER'] : '';
    }

    private function refererHost() {
        $referer = $this->referer();
        if ($referer === '') {
            return '';
        }

        return strtolower((string)parse_url($referer, PHP_URL_HOST));
    }

    private function logError($context, Exception $e) {
        $log = $this->registry->get('log');
        if ($log) {
            $log->write('Egeser Visitor Tracker (' . $context . '): ' . $e->getMessage());
        }
    }
}
