<?php
class ControllerExtensionModuleEgeserVisitorReport extends Controller {
    private $error = array();
    private $permission_key = 'extension/module/egeser_visitor_report';

    public function index() {
        $this->dashboard();
    }

    // ------------------------------------------------------------------
    // Dashboard
    // ------------------------------------------------------------------

    public function dashboard() {
        if (!$this->checkPermission()) return;

        $this->load->language('extension/module/egeser_visitor_report');
        $this->load->model('extension/module/egeser_visitor_report');
        $this->model_extension_module_egeser_visitor_report->install();

        $range = $this->resolveDateRange();

        $data = $this->commonData('dashboard', $range);
        $data['kpis'] = $this->model_extension_module_egeser_visitor_report->getKpis($range['date_from'], $range['date_to']);
        $data['top_products'] = $this->model_extension_module_egeser_visitor_report->getTopProductsShort($range['date_from'], $range['date_to'], 5);
        $data['top_sources'] = $this->model_extension_module_egeser_visitor_report->getTopSourcesShort($range['date_from'], $range['date_to'], 5);
        $data['recent_conversions'] = $this->model_extension_module_egeser_visitor_report->getRecentConversions(10);
        $data['live_visitors'] = $this->model_extension_module_egeser_visitor_report->getLiveVisitors();

        $this->response->setOutput($this->load->view('extension/module/egeser_visitor_report_dashboard', $data));
    }

    // ------------------------------------------------------------------
    // Canli Ziyaretciler
    // ------------------------------------------------------------------

    public function live() {
        if (!$this->checkPermission()) return;

        $this->load->language('extension/module/egeser_visitor_report');
        $this->load->model('extension/module/egeser_visitor_report');
        $this->model_extension_module_egeser_visitor_report->install();

        $data = $this->commonData('live', $this->resolveDateRange());
        $data['live_visitors'] = $this->model_extension_module_egeser_visitor_report->getLiveVisitors();
        $data['live_data_url'] = $this->url->link('extension/module/egeser_visitor_report/liveData', 'token=' . $this->session->data['token'], true);

        $this->response->setOutput($this->load->view('extension/module/egeser_visitor_report_live', $data));
    }

    public function liveData() {
        if (!$this->user->hasPermission('access', $this->permission_key)) {
            $this->response->setOutput(json_encode(array('error' => 'permission')));
            return;
        }

        $this->load->model('extension/module/egeser_visitor_report');
        $visitors = $this->model_extension_module_egeser_visitor_report->getLiveVisitors();

        $rows = array();
        foreach ($visitors as $v) {
            $rows[] = array(
                'visitor_label' => '#' . strtoupper(substr((string)$v['visitor_token'], 0, 6)),
                'source' => $this->sourceLabel($v['source'], $v['medium']),
                'landing_page' => $v['landing_page'],
                'current_page' => $v['current_page'] ?: $v['current_url'],
                'device_type' => $v['device_type'],
                'duration' => $this->humanDuration($v['started_at'], $v['last_activity']),
                'pageviews' => (int)$v['pageviews'],
                'whatsapp_clicked' => !empty($v['whatsapp_clicked']),
                'quote_started' => !empty($v['quote_started']),
                'converted' => !empty($v['converted'])
            );
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode(array('visitors' => $rows, 'count' => count($rows))));
    }

    // ------------------------------------------------------------------
    // Gunluk Rapor
    // ------------------------------------------------------------------

    public function daily() {
        if (!$this->checkPermission()) return;

        $this->load->language('extension/module/egeser_visitor_report');
        $this->load->model('extension/module/egeser_visitor_report');
        $this->model_extension_module_egeser_visitor_report->install();

        $range = $this->resolveDateRange();

        $data = $this->commonData('daily', $range);
        $data['kpis'] = $this->model_extension_module_egeser_visitor_report->getKpis($range['date_from'], $range['date_to']);
        $data['hourly'] = $this->model_extension_module_egeser_visitor_report->getHourlyTraffic($range['date_from'], $range['date_to']);
        $data['sources'] = $this->model_extension_module_egeser_visitor_report->getSourceDistribution($range['date_from'], $range['date_to']);
        $data['devices'] = $this->model_extension_module_egeser_visitor_report->getDeviceDistribution($range['date_from'], $range['date_to']);
        $data['top_products'] = $this->model_extension_module_egeser_visitor_report->getTopProductsShort($range['date_from'], $range['date_to'], 10);

        $this->response->setOutput($this->load->view('extension/module/egeser_visitor_report_daily', $data));
    }

    // ------------------------------------------------------------------
    // Ziyaretci Yolculuklari
    // ------------------------------------------------------------------

    public function journeys() {
        if (!$this->checkPermission()) return;

        $this->load->language('extension/module/egeser_visitor_report');
        $this->load->model('extension/module/egeser_visitor_report');
        $this->model_extension_module_egeser_visitor_report->install();

        $range = $this->resolveDateRange();
        $only_converted = !empty($this->request->get['converted_only']);
        $page = isset($this->request->get['page']) ? max(1, (int)$this->request->get['page']) : 1;
        $limit = 25;

        $data = $this->commonData('journeys', $range);
        $data['only_converted'] = $only_converted;
        $data['toggle_converted_url'] = $this->url->link('extension/module/egeser_visitor_report/journeys',
            'token=' . $this->session->data['token'] . '&range=' . $range['range'] . '&date_from=' . $range['date_from'] . '&date_to=' . $range['date_to'] . '&converted_only=' . ($only_converted ? 0 : 1),
            true);

        $journeys = $this->model_extension_module_egeser_visitor_report->getVisitorJourneys(
            $range['date_from'], $range['date_to'], ($page - 1) * $limit, $limit, $only_converted
        );
        foreach ($journeys as &$journey) {
            $journey['detail_url'] = $this->url->link('extension/module/egeser_visitor_report/journeyDetail', 'token=' . $this->session->data['token'] . '&session_id=' . $journey['session_id'], true);
        }
        unset($journey);
        $data['journeys'] = $journeys;

        $total = $this->model_extension_module_egeser_visitor_report->getTotalVisitorJourneys($range['date_from'], $range['date_to'], $only_converted);
        $data['pagination'] = $this->buildPagination($total, $page, $limit, 'journeys', $range, array('converted_only' => $only_converted ? 1 : 0));

        $this->response->setOutput($this->load->view('extension/module/egeser_visitor_report_journeys', $data));
    }

    public function journeyDetail() {
        if (!$this->checkPermission()) return;

        $this->load->language('extension/module/egeser_visitor_report');
        $this->load->model('extension/module/egeser_visitor_report');

        $session_id = isset($this->request->get['session_id']) ? (int)$this->request->get['session_id'] : 0;

        $data = $this->commonData('journeys', $this->resolveDateRange());
        $data['session'] = $this->model_extension_module_egeser_visitor_report->getSession($session_id);
        $data['timeline'] = $this->model_extension_module_egeser_visitor_report->getSessionTimeline($session_id);

        if ($data['session']) {
            $data['visitor_session_count'] = $this->model_extension_module_egeser_visitor_report->getVisitorSessionCount($data['session']['visitor_id']);

            if (!empty($data['session']['lead_id'])) {
                $this->load->model('sale/egeser_lead');
                $data['lead'] = $this->model_sale_egeser_lead->getLead($data['session']['lead_id']);
            } else {
                $data['lead'] = array();
            }
        } else {
            $data['visitor_session_count'] = 0;
            $data['lead'] = array();
        }

        $this->response->setOutput($this->load->view('extension/module/egeser_visitor_report_journey_detail', $data));
    }

    // ------------------------------------------------------------------
    // Urun Raporlari
    // ------------------------------------------------------------------

    public function products() {
        if (!$this->checkPermission()) return;

        $this->load->language('extension/module/egeser_visitor_report');
        $this->load->model('extension/module/egeser_visitor_report');
        $this->model_extension_module_egeser_visitor_report->install();

        $range = $this->resolveDateRange();
        $page = isset($this->request->get['page']) ? max(1, (int)$this->request->get['page']) : 1;
        $limit = 25;

        $data = $this->commonData('products', $range);
        $data['products'] = $this->model_extension_module_egeser_visitor_report->getProductReport(
            $range['date_from'], $range['date_to'], ($page - 1) * $limit, $limit
        );

        $total = $this->model_extension_module_egeser_visitor_report->getTotalProducts($range['date_from'], $range['date_to']);
        $data['pagination'] = $this->buildPagination($total, $page, $limit, 'products', $range, array());

        $this->response->setOutput($this->load->view('extension/module/egeser_visitor_report_products', $data));
    }

    // ------------------------------------------------------------------
    // Trafik Kaynaklari
    // ------------------------------------------------------------------

    public function sources() {
        if (!$this->checkPermission()) return;

        $this->load->language('extension/module/egeser_visitor_report');
        $this->load->model('extension/module/egeser_visitor_report');
        $this->model_extension_module_egeser_visitor_report->install();

        $range = $this->resolveDateRange();

        $data = $this->commonData('sources', $range);
        $data['sources'] = $this->model_extension_module_egeser_visitor_report->getTrafficSources($range['date_from'], $range['date_to']);

        $this->response->setOutput($this->load->view('extension/module/egeser_visitor_report_sources', $data));
    }

    // ------------------------------------------------------------------
    // Donusumler
    // ------------------------------------------------------------------

    public function conversions() {
        if (!$this->checkPermission()) return;

        $this->load->language('extension/module/egeser_visitor_report');
        $this->load->model('extension/module/egeser_visitor_report');
        $this->model_extension_module_egeser_visitor_report->install();

        $range = $this->resolveDateRange();
        $page = isset($this->request->get['page']) ? max(1, (int)$this->request->get['page']) : 1;
        $limit = 25;

        $data = $this->commonData('conversions', $range);
        $data['conversions'] = $this->model_extension_module_egeser_visitor_report->getConversions(
            $range['date_from'], $range['date_to'], ($page - 1) * $limit, $limit
        );

        $total = $this->model_extension_module_egeser_visitor_report->getTotalConversions($range['date_from'], $range['date_to']);
        $data['pagination'] = $this->buildPagination($total, $page, $limit, 'conversions', $range, array());

        $this->response->setOutput($this->load->view('extension/module/egeser_visitor_report_conversions', $data));
    }

    // ------------------------------------------------------------------
    // Ayarlar
    // ------------------------------------------------------------------

    public function settings() {
        if (!$this->user->hasPermission('modify', $this->permission_key)) {
            $this->response->redirect($this->url->link('error/permission', 'token=' . $this->session->data['token'], true));
            return;
        }

        $this->load->language('extension/module/egeser_visitor_report');
        $this->load->model('extension/module/egeser_visitor_report');
        $this->load->model('setting/setting');

        $data = $this->commonData('settings', $this->resolveDateRange());

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($this->request->post['egeser_visitor_event_retention'])) {
            $post = array(
                'egeser_visitor_event_retention' => max(7, min(3650, (int)$this->request->post['egeser_visitor_event_retention'])),
                'egeser_visitor_session_retention' => max(7, min(3650, (int)$this->request->post['egeser_visitor_session_retention'])),
                'egeser_visitor_retention' => max(30, min(3650, (int)$this->request->post['egeser_visitor_retention']))
            );
            $this->model_setting_setting->editSetting('egeser_visitor', $post);
            $data['success'] = $this->language->get('text_success');
        }

        if (!empty($this->request->post['egeser_visitor_cleanup_now'])) {
            $result = $this->model_extension_module_egeser_visitor_report->cleanup(
                (int)$this->config->get('egeser_visitor_event_retention') ?: 90,
                (int)$this->config->get('egeser_visitor_session_retention') ?: 180,
                (int)$this->config->get('egeser_visitor_retention') ?: 365
            );
            $data['success'] = sprintf($this->language->get('text_cleanup_result'), $result['events_deleted'], $result['sessions_deleted'], $result['visitors_deleted']);
        }

        $data['egeser_visitor_event_retention'] = (int)$this->config->get('egeser_visitor_event_retention') ?: 90;
        $data['egeser_visitor_session_retention'] = (int)$this->config->get('egeser_visitor_session_retention') ?: 180;
        $data['egeser_visitor_retention'] = (int)$this->config->get('egeser_visitor_retention') ?: 365;
        $data['action'] = $this->url->link('extension/module/egeser_visitor_report/settings', 'token=' . $this->session->data['token'], true);

        $this->response->setOutput($this->load->view('extension/module/egeser_visitor_report_settings', $data));
    }

    /**
     * Cron ile de tetiklenebilir: egeser_health'in cron-key desenine benzer,
     * ama basitlik icin ayni cron key config anahtari yeniden kullanilir.
     */
    public function cron() {
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');

        $key = isset($this->request->server['HTTP_X_EGESER_CRON_KEY']) ? $this->request->server['HTTP_X_EGESER_CRON_KEY'] : '';
        $expected = (string)$this->config->get('egeser_health_cron_key');

        if (!$expected || !hash_equals($expected, (string)$key)) {
            $this->response->addHeader('HTTP/1.1 403 Forbidden');
            $this->response->setOutput(json_encode(array('ok' => false)));
            return;
        }

        $this->load->model('extension/module/egeser_visitor_report');
        $result = $this->model_extension_module_egeser_visitor_report->cleanup(
            (int)$this->config->get('egeser_visitor_event_retention') ?: 90,
            (int)$this->config->get('egeser_visitor_session_retention') ?: 180,
            (int)$this->config->get('egeser_visitor_retention') ?: 365
        );

        $this->response->setOutput(json_encode(array('ok' => true) + $result));
    }

    // ------------------------------------------------------------------
    // CSV Export
    // ------------------------------------------------------------------

    public function exportCsv() {
        if (!$this->checkPermission()) return;

        $this->load->model('extension/module/egeser_visitor_report');
        $range = $this->resolveDateRange();
        $report = isset($this->request->get['report']) ? (string)$this->request->get['report'] : 'products';

        $rows = array();
        $header = array();
        $filename = 'egeser-' . $report . '-' . date('Ymd') . '.csv';

        if ($report === 'sources') {
            $header = array('Kaynak', 'Oturum', 'Ziyaretçi', 'Sayfa Görüntüleme', 'Ürün Görüntüleme', 'WhatsApp', 'Dönüşüm', 'Dönüşüm Oranı (%)');
            foreach ($this->model_extension_module_egeser_visitor_report->getTrafficSources($range['date_from'], $range['date_to']) as $r) {
                $rows[] = array($r['source'], $r['sessions'], $r['visitors'], $r['pageviews'], $r['product_views'], $r['whatsapp_clicks'], $r['conversions'], $r['conversion_rate']);
            }
        } elseif ($report === 'conversions') {
            $header = array('Tarih', 'Ziyaretçi', 'Tür', 'Kaynak', 'Kampanya', 'Lead ID');
            foreach ($this->model_extension_module_egeser_visitor_report->getConversions($range['date_from'], $range['date_to'], 0, 5000) as $r) {
                $rows[] = array($r['created_at'], '#' . strtoupper(substr((string)$r['visitor_token'], 0, 6)), $r['conversion_type'], $r['source'], $r['campaign'], $r['lead_id']);
            }
        } elseif ($report === 'journeys') {
            $header = array('Başlangıç', 'Ziyaretçi', 'Kaynak', 'Giriş Sayfası', 'Sayfa Görüntüleme', 'Dönüşüm');
            foreach ($this->model_extension_module_egeser_visitor_report->getVisitorJourneys($range['date_from'], $range['date_to'], 0, 5000, false) as $r) {
                $rows[] = array($r['started_at'], '#' . strtoupper(substr((string)$r['visitor_token'], 0, 6)), $r['source'], $r['landing_page'], $r['pageviews'], $r['converted'] ? 'Evet' : 'Hayır');
            }
        } else {
            $header = array('Ürün', 'Görüntülenme', 'Benzersiz Ziyaretçi', 'WhatsApp', 'Telefon', 'Teklif', 'Dönüşüm Oranı (%)');
            foreach ($this->model_extension_module_egeser_visitor_report->getProductReport($range['date_from'], $range['date_to'], 0, 5000) as $r) {
                $rows[] = array($r['name'], $r['views'], $r['visitors'], $r['whatsapp_clicks'], $r['phone_clicks'], $r['quote_forms'], $r['conversion_rate']);
            }
        }

        $this->response->addHeader('Content-Type: text/csv; charset=utf-8');
        $this->response->addHeader('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://temp', 'w+');
        fwrite($output, "\xEF\xBB\xBF"); // UTF-8 BOM, Excel/Turkce uyumlulugu
        fputcsv($output, $header);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        $this->response->setOutput($csv);
    }

    // ------------------------------------------------------------------
    // Kurulum / kaldirma (Extensions > Modules)
    // ------------------------------------------------------------------

    public function install() {
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', $this->permission_key);
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', $this->permission_key);

        $this->load->model('extension/module/egeser_visitor_report');
        $this->model_extension_module_egeser_visitor_report->install();
    }

    public function uninstall() {
        // Ziyaretci verisi (egeser_visitor/session/event/conversion) kasitli
        // olarak silinmez; sadece modul kaydi kaldirilir. Veri Ayarlar
        // ekranindaki "Simdi Temizle" veya retention cron ile yonetilir.
    }

    // ------------------------------------------------------------------
    // Ortak yardimcilar
    // ------------------------------------------------------------------

    private function checkPermission() {
        if (!$this->user->hasPermission('access', $this->permission_key)) {
            $this->response->redirect($this->url->link('error/permission', 'token=' . $this->session->data['token'], true));
            return false;
        }
        return true;
    }

    private function resolveDateRange() {
        $range = isset($this->request->get['range']) ? (string)$this->request->get['range'] : 'today';
        $today = date('Y-m-d');

        switch ($range) {
            case 'yesterday':
                $from = $to = date('Y-m-d', strtotime('-1 day'));
                break;
            case '7days':
                $from = date('Y-m-d', strtotime('-6 days'));
                $to = $today;
                break;
            case '30days':
                $from = date('Y-m-d', strtotime('-29 days'));
                $to = $today;
                break;
            case 'month':
                $from = date('Y-m-01');
                $to = $today;
                break;
            case 'custom':
                $from = isset($this->request->get['date_from']) ? (string)$this->request->get['date_from'] : $today;
                $to = isset($this->request->get['date_to']) ? (string)$this->request->get['date_to'] : $today;
                break;
            case 'today':
            default:
                $range = 'today';
                $from = $to = $today;
                break;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = $today;
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) $to = $today;
        if ($from > $to) { $tmp = $from; $from = $to; $to = $tmp; }

        return array('range' => $range, 'date_from' => $from, 'date_to' => $to);
    }

    private function commonData($active, $range) {
        $this->document->setTitle($this->language->get('heading_title'));

        $token = 'token=' . $this->session->data['token'];

        $tabs = array(
            'dashboard' => array('key' => 'dashboard', 'text' => $this->language->get('tab_dashboard'), 'route' => 'extension/module/egeser_visitor_report/dashboard'),
            'live' => array('key' => 'live', 'text' => $this->language->get('tab_live'), 'route' => 'extension/module/egeser_visitor_report/live'),
            'daily' => array('key' => 'daily', 'text' => $this->language->get('tab_daily'), 'route' => 'extension/module/egeser_visitor_report/daily'),
            'journeys' => array('key' => 'journeys', 'text' => $this->language->get('tab_journeys'), 'route' => 'extension/module/egeser_visitor_report/journeys'),
            'products' => array('key' => 'products', 'text' => $this->language->get('tab_products'), 'route' => 'extension/module/egeser_visitor_report/products'),
            'sources' => array('key' => 'sources', 'text' => $this->language->get('tab_sources'), 'route' => 'extension/module/egeser_visitor_report/sources'),
            'conversions' => array('key' => 'conversions', 'text' => $this->language->get('tab_conversions'), 'route' => 'extension/module/egeser_visitor_report/conversions'),
            'settings' => array('key' => 'settings', 'text' => $this->language->get('tab_settings'), 'route' => 'extension/module/egeser_visitor_report/settings')
        );

        foreach ($tabs as &$tab) {
            $suffix = ($tab['key'] === 'settings') ? '' : '&range=' . $range['range'] . '&date_from=' . $range['date_from'] . '&date_to=' . $range['date_to'];
            $tab['href'] = $this->url->link($tab['route'], $token . $suffix, true);
            $tab['active'] = ($tab['key'] === $active);
        }

        $base_route = isset($tabs[$active]) ? $tabs[$active]['route'] : 'extension/module/egeser_visitor_report/dashboard';

        $range_keys = array('today', 'yesterday', '7days', '30days', 'month');
        $range_links = array();
        foreach ($range_keys as $key) {
            $range_links[$key] = $this->url->link($base_route, $token . '&range=' . $key, true);
        }

        $data = array();
        $data['tabs'] = $tabs;
        $data['active_tab'] = $active;
        $data['heading_title'] = $this->language->get('heading_title');
        $data['token'] = $this->session->data['token'];
        $data['range'] = $range['range'];
        $data['date_from'] = $range['date_from'];
        $data['date_to'] = $range['date_to'];
        $data['range_links'] = $range_links;
        $data['base_route'] = $base_route;
        $data['export_url'] = $this->url->link('extension/module/egeser_visitor_report/exportCsv', $token . '&report=' . $active . '&range=' . $range['range'] . '&date_from=' . $range['date_from'] . '&date_to=' . $range['date_to'], true);

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/module/egeser_visitor_report/dashboard', $token, true));

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
        unset($this->session->data['success']);

        return $data;
    }

    private function buildPagination($total, $page, $limit, $route_key, $range, $extra) {
        $pages = (int)ceil($total / $limit);
        $qs = '&range=' . $range['range'] . '&date_from=' . $range['date_from'] . '&date_to=' . $range['date_to'];
        foreach ($extra as $k => $v) {
            $qs .= '&' . $k . '=' . $v;
        }

        return array(
            'total' => $total,
            'page' => $page,
            'pages' => $pages,
            'prev' => $page > 1 ? $this->url->link('extension/module/egeser_visitor_report/' . $route_key, 'token=' . $this->session->data['token'] . $qs . '&page=' . ($page - 1), true) : '',
            'next' => $page < $pages ? $this->url->link('extension/module/egeser_visitor_report/' . $route_key, 'token=' . $this->session->data['token'] . $qs . '&page=' . ($page + 1), true) : ''
        );
    }

    private function sourceLabel($source, $medium) {
        $map = array(
            'google' => $medium === 'cpc' ? 'Google Ads' : 'Google Organic',
            'facebook' => $medium === 'cpc' ? 'Facebook Ads' : 'Meta Organic',
            'instagram' => 'Instagram',
            'direct' => 'Direct'
        );

        if (isset($map[$source])) {
            return $map[$source];
        }

        return $medium === 'referral' ? ('Referral: ' . $source) : ucfirst((string)$source);
    }

    private function humanDuration($start, $end) {
        $seconds = max(0, strtotime($end) - strtotime($start));
        $minutes = floor($seconds / 60);
        $sec = $seconds % 60;
        return sprintf('%d:%02d', $minutes, $sec);
    }
}
