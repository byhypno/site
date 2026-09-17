<?php
class ControllerExtensionModuleEgeserHealth extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_health');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');
        $this->load->model('extension/module/egeser_health');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $post = $this->request->post;
            if (empty($post['egeser_health_cron_key']) || strlen($post['egeser_health_cron_key']) < 32) {
                $post['egeser_health_cron_key'] = $this->generateKey();
            }
            $this->model_setting_setting->editSetting('egeser_health', $post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module/egeser_health', 'token=' . $this->session->data['token'], true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_run'] = $this->language->get('button_run');
        $data['button_test_mail'] = $this->language->get('button_test_mail');

        // Admin template language labels
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_whatsapp'] = $this->language->get('entry_whatsapp');
        $data['entry_lead_recipient'] = $this->language->get('entry_lead_recipient');
        $data['entry_retention'] = $this->language->get('entry_retention');
        $data['help_retention'] = $this->language->get('help_retention');

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';
        unset($this->session->data['success']);

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array('text'=>$this->language->get('text_home'),'href'=>$this->url->link('common/dashboard','token='.$this->session->data['token'],true));
        $data['breadcrumbs'][] = array('text'=>$this->language->get('text_extension'),'href'=>$this->url->link('extension/extension','token='.$this->session->data['token'].'&type=module',true));
        $data['breadcrumbs'][] = array('text'=>$this->language->get('heading_title'),'href'=>$this->url->link('extension/module/egeser_health','token='.$this->session->data['token'],true));

        $data['action'] = $this->url->link('extension/module/egeser_health', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
        $data['run_url'] = html_entity_decode($this->url->link('extension/module/egeser_health/run', 'token=' . $this->session->data['token'], true), ENT_QUOTES, 'UTF-8');
        $data['scan_url'] = html_entity_decode($this->url->link('extension/module/egeser_health/scan', 'token=' . $this->session->data['token'], true), ENT_QUOTES, 'UTF-8');
        $data['mail_url'] = html_entity_decode($this->url->link('extension/module/egeser_health/testMail', 'token=' . $this->session->data['token'], true), ENT_QUOTES, 'UTF-8');
        $data['performance_url'] = html_entity_decode($this->url->link('extension/module/egeser_health/performance', 'token=' . $this->session->data['token'], true), ENT_QUOTES, 'UTF-8');
        $data['redirect_audit_url'] = html_entity_decode($this->url->link('extension/module/egeser_health/redirectAudit', 'token=' . $this->session->data['token'], true), ENT_QUOTES, 'UTF-8');

        $fields = array(
            'egeser_health_status' => 1,
            'egeser_health_whatsapp' => '',
            'egeser_health_lead_recipient' => $this->config->get('config_email'),
            'egeser_health_retention_days' => 90,
            'egeser_health_scan_max_pages' => 40,
            'egeser_health_tracking_status' => 0,
            'egeser_health_ga4_id' => '',
            'egeser_health_meta_pixel_id' => '',
            'egeser_health_google_ads_id' => '',
            'egeser_health_google_ads_lead_label' => '',
            'egeser_health_cron_key' => '',
            'egeser_performance_cron_status' => 1,
            'egeser_performance_content_visibility' => 1,
            'egeser_security_search_noindex' => 1,
            'egeser_security_search_rate_limit' => 30,
            'egeser_security_spam_logging' => 1,
            'egeser_security_ip_salt' => '',
            'egeser_site_instagram' => '',
            'egeser_site_facebook' => '',
            'egeser_site_tiktok' => '',
            'egeser_site_youtube' => '',
            'egeser_site_blog_url' => '',
            'egeser_site_technical_url' => ''
        );

        foreach ($fields as $key => $default) {
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } else {
                $saved = $this->config->get($key);
                $data[$key] = ($saved !== null && $saved !== '') ? $saved : $default;
            }
        }

        if (!$data['egeser_health_cron_key']) $data['egeser_health_cron_key'] = $this->generateKey();

        $store_url = $this->config->get('config_ssl') ? $this->config->get('config_ssl') : $this->config->get('config_url');

        if (!$store_url && defined('HTTP_CATALOG')) {
            $store_url = HTTP_CATALOG;
        }

        if (!$store_url && defined('HTTPS_CATALOG')) {
            $store_url = HTTPS_CATALOG;
        }

        if (!$store_url && isset($this->request->server['HTTP_HOST'])) {
            $scheme = (!empty($this->request->server['HTTPS']) && $this->request->server['HTTPS'] != 'off') ? 'https://' : 'http://';
            $store_url = $scheme . $this->request->server['HTTP_HOST'] . '/';
        }

        $cron_endpoint = rtrim($store_url, '/') . '/index.php?route=extension/module/egeser_health_cron';
        $data['cron_endpoint'] = $cron_endpoint;
        $data['cron_command'] = 'curl -fsS -H "X-Egeser-Cron-Key: ' . $data['egeser_health_cron_key'] . '" "' . $cron_endpoint . '" >/dev/null';

        $data['report'] = $this->model_extension_module_egeser_health->getReport();
        $data['lead_stats'] = $this->model_extension_module_egeser_health->getLeadStats();
        $data['recent_leads'] = $this->model_extension_module_egeser_health->getRecentLeads(10);
        $data['recent_runs'] = $this->model_extension_module_egeser_health->getRecentRuns(10);
        $data['open_issues'] = $this->model_extension_module_egeser_health->getOpenIssues(25);
        $data['recent_performance_runs'] = $this->model_extension_module_egeser_health->getRecentPerformanceRuns(10);
        $data['latest_performance'] = $this->model_extension_module_egeser_health->getLatestPerformance();
        $data['security_stats'] = $this->model_extension_module_egeser_health->getSecurityStats();
        $data['recent_spam'] = $this->model_extension_module_egeser_health->getRecentSpam(25);
        $data['recent_redirect_audits'] = $this->model_extension_module_egeser_health->getRecentRedirectAudits(20);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/egeser_health.tpl', $data));
    }

    public function install() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_health')) return;
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/egeser_health');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/egeser_health');
        $this->load->model('extension/module/egeser_health');
        $this->model_extension_module_egeser_health->install();

        $this->load->model('setting/setting');
        $current = $this->model_setting_setting->getSetting('egeser_health');
        if (empty($current['egeser_health_cron_key'])) {
            $current['egeser_health_cron_key'] = $this->generateKey();
        }
        if (!isset($current['egeser_health_status'])) $current['egeser_health_status'] = 1;
        if (!isset($current['egeser_health_scan_max_pages'])) $current['egeser_health_scan_max_pages'] = 40;
        if (!isset($current['egeser_health_tracking_status'])) $current['egeser_health_tracking_status'] = 0;
        if (!isset($current['egeser_performance_cron_status'])) $current['egeser_performance_cron_status'] = 1;
        if (!isset($current['egeser_performance_content_visibility'])) $current['egeser_performance_content_visibility'] = 1;
        if (!isset($current['egeser_security_search_noindex'])) $current['egeser_security_search_noindex'] = 1;
        if (!isset($current['egeser_security_search_rate_limit'])) $current['egeser_security_search_rate_limit'] = 30;
        if (!isset($current['egeser_security_spam_logging'])) $current['egeser_security_spam_logging'] = 1;
        if (empty($current['egeser_security_ip_salt'])) $current['egeser_security_ip_salt'] = $this->generateKey();
        $this->model_setting_setting->editSetting('egeser_health', $current);
    }

    public function uninstall() {
        // Lead, sağlık ve 404 geçmişi yanlışlıkla silinmesin diye tablolar korunur.
    }

    public function run() {
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');

        if (!$this->user->hasPermission('access', 'extension/module/egeser_health')) {
            $this->response->setOutput(json_encode(array('error'=>'Yetkisiz işlem.')));
            return;
        }

        // Admin AJAX endpoint'lerinde PHP Notice/Warning HTML'i JSON yanıtını bozmasın.
        $old_display_errors = ini_get('display_errors');
        @ini_set('display_errors', '0');
        ob_start();

        try {
            $this->load->model('extension/module/egeser_health');
            $days = (int)$this->config->get('egeser_health_retention_days');
            if ($days < 30) $days = 90;

            $this->model_extension_module_egeser_health->cleanup($days);
            $result = $this->model_extension_module_egeser_health->runHealth(false, 0, 'manual');

            $noise = trim((string)ob_get_clean());
            if ($noise !== '') {
                $this->log->write('Egeser Health AJAX notice output: ' . strip_tags($noise));
            }

            @ini_set('display_errors', $old_display_errors);
            $this->response->setOutput(json_encode(array(
                'success'=>true,
                'score'=>isset($result['score']) ? (int)$result['score'] : 0,
                'status'=>isset($result['status']) ? $result['status'] : ''
            )));
        } catch (Throwable $e) {
            if (ob_get_level()) ob_end_clean();
            @ini_set('display_errors', $old_display_errors);
            $this->log->write('Egeser Health Run Throwable: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            $this->response->setOutput(json_encode(array('error'=>'Sağlık kontrolü sunucuda tamamlanamadı. Hata günlüğü kaydedildi.')));
        } catch (Exception $e) {
            if (ob_get_level()) ob_end_clean();
            @ini_set('display_errors', $old_display_errors);
            $this->log->write('Egeser Health Run Exception: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
            $this->response->setOutput(json_encode(array('error'=>'Sağlık kontrolü sunucuda tamamlanamadı. Hata günlüğü kaydedildi.')));
        }
    }

    public function scan() {
        $this->jsonPermission('modify');
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_health')) return;

        $this->load->model('extension/module/egeser_health');
        $max_pages = (int)$this->config->get('egeser_health_scan_max_pages');
        if ($max_pages < 5) $max_pages = 40;
        $result = $this->model_extension_module_egeser_health->runHealth(true, min(100, $max_pages), 'manual_scan');

        if (empty($result['scan']['scan_valid'])) {
            $this->response->setOutput(json_encode(array(
                'error'=>'Tarama başlatıldı ancak frontend sayfası okunamadı. Katalog URL yapılandırmasını kontrol edin.',
                'score'=>$result['score'],
                'status'=>$result['status'],
                'scan'=>$result['scan']
            )));
            return;
        }

        $this->response->setOutput(json_encode(array(
            'success'=>true,
            'score'=>$result['score'],
            'status'=>$result['status'],
            'scan'=>$result['scan']
        )));
    }



    public function redirectAudit() {
        $this->jsonPermission('modify');
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_health')) return;

        $url = isset($this->request->get['url']) ? trim((string)$this->request->get['url']) : '';
        $base = $this->config->get('config_ssl') ? $this->config->get('config_ssl') : $this->config->get('config_url');
        $base_host = strtolower((string)parse_url($base, PHP_URL_HOST));
        $host = strtolower((string)parse_url($url, PHP_URL_HOST));

        if (!$url || !$host || $host !== $base_host) {
            $this->response->setOutput(json_encode(array('error'=>'Yalnızca mağazanın kendi URL adresleri denetlenebilir.')));
            return;
        }

        $this->load->model('extension/module/egeser_health');

        try {
            $result = $this->model_extension_module_egeser_health->auditRedirect($url);
            $this->response->setOutput(json_encode(array(
                'success'=>true,
                'status'=>$result['status'],
                'hop_count'=>$result['hop_count'],
                'final_url'=>$result['final_url'],
                'chain'=>$result['chain']
            )));
        } catch (Exception $e) {
            $this->log->write('Egeser Redirect Audit Error: ' . $e->getMessage());
            $this->response->setOutput(json_encode(array('error'=>'Redirect denetimi tamamlanamadı.')));
        }
    }

    public function performance() {
        $this->jsonPermission('modify');
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_health')) return;

        $this->load->model('extension/module/egeser_health');
        try {
            $result = $this->model_extension_module_egeser_health->runPerformance('manual');
            $this->response->setOutput(json_encode(array(
                'success'=>true,
                'score'=>$result['score'],
                'status'=>$result['status'],
                'pages_tested'=>$result['pages_tested'],
                'avg_response_ms'=>$result['avg_response_ms'],
                'avg_html_kb'=>$result['avg_html_kb'],
                'issues_found'=>$result['issues_found']
            )));
        } catch (Exception $e) {
            $this->log->write('Egeser Performance Audit Error: ' . $e->getMessage());
            $this->response->setOutput(json_encode(array('error'=>'Performans denetimi tamamlanamadı. Hata günlüğünü kontrol edin.')));
        }
    }

    public function testMail() {
        $this->jsonPermission('modify');
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_health')) return;

        $to = $this->config->get('egeser_health_lead_recipient') ? $this->config->get('egeser_health_lead_recipient') : $this->config->get('config_email');
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->response->setOutput(json_encode(array('error'=>'Geçerli test alıcı e-postası bulunamadı.')));
            return;
        }

        $old_display_errors = ini_get('display_errors');
        @ini_set('display_errors', '0');

        // PHP mail()/SMTP uyarılarını JSON yanıtını bozmadan gerçek hata olarak yakala.
        set_error_handler(function($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        });

        try {
            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
            $mail->setTo($to);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
            $mail->setSubject('[EGESER TEST] Site Kontrol Merkezi V10');
            $mail->setText("Egeser Site Kontrol Merkezi V10 test e-postası.
Tarih: " . date('Y-m-d H:i:s'));
            $mail->send();

            restore_error_handler();
            @ini_set('display_errors', $old_display_errors);
            $this->response->setOutput(json_encode(array('success'=>'Test e-postası gönderim komutu başarılı: '.$to)));
        } catch (Throwable $e) {
            restore_error_handler();
            @ini_set('display_errors', $old_display_errors);
            $this->log->write('Egeser Test Mail Error: ' . $e->getMessage());
            $message = $e->getMessage();
            if (stripos($message, 'mail() has been disabled') !== false) {
                $message = 'Sunucuda PHP mail() kapalı. OpenCart Mail ayarını SMTP olarak yapılandırın.';
            }
            $this->response->setOutput(json_encode(array('error'=>'Mail gönderimi başarısız: '.$message)));
        } catch (Exception $e) {
            restore_error_handler();
            @ini_set('display_errors', $old_display_errors);
            $this->log->write('Egeser Test Mail Error: ' . $e->getMessage());
            $this->response->setOutput(json_encode(array('error'=>'Mail gönderimi başarısız: '.$e->getMessage())));
        }
    }

    private function jsonPermission($level) {
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        if (!$this->user->hasPermission($level, 'extension/module/egeser_health')) {
            $this->response->setOutput(json_encode(array('error'=>'Yetkisiz işlem.')));
        }
    }

    private function generateKey() {
        if (function_exists('random_bytes')) return bin2hex(random_bytes(24));
        return sha1(uniqid(mt_rand(), true)) . sha1(uniqid(mt_rand(), true));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_health')) {
            $this->error['warning'] = $this->language->get('error_permission');
            return false;
        }

        $email = isset($this->request->post['egeser_health_lead_recipient']) ? trim($this->request->post['egeser_health_lead_recipient']) : '';
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error['warning'] = 'Lead alıcı e-posta adresi geçersiz.';
        }

        $ga4 = isset($this->request->post['egeser_health_ga4_id']) ? trim($this->request->post['egeser_health_ga4_id']) : '';
        $meta = isset($this->request->post['egeser_health_meta_pixel_id']) ? trim($this->request->post['egeser_health_meta_pixel_id']) : '';
        $ads = isset($this->request->post['egeser_health_google_ads_id']) ? trim($this->request->post['egeser_health_google_ads_id']) : '';
        $label = isset($this->request->post['egeser_health_google_ads_lead_label']) ? trim($this->request->post['egeser_health_google_ads_lead_label']) : '';

        $site_urls = array(
            'Instagram' => isset($this->request->post['egeser_site_instagram']) ? trim($this->request->post['egeser_site_instagram']) : '',
            'Facebook' => isset($this->request->post['egeser_site_facebook']) ? trim($this->request->post['egeser_site_facebook']) : '',
            'TikTok' => isset($this->request->post['egeser_site_tiktok']) ? trim($this->request->post['egeser_site_tiktok']) : '',
            'YouTube' => isset($this->request->post['egeser_site_youtube']) ? trim($this->request->post['egeser_site_youtube']) : '',
            'Blog' => isset($this->request->post['egeser_site_blog_url']) ? trim($this->request->post['egeser_site_blog_url']) : '',
            'Teknik Bilgiler' => isset($this->request->post['egeser_site_technical_url']) ? trim($this->request->post['egeser_site_technical_url']) : ''
        );
        foreach ($site_urls as $site_label => $site_url) {
            if ($site_url && !filter_var($site_url, FILTER_VALIDATE_URL)) {
                $this->error['warning'] = $site_label . ' URL adresi geçersiz.';
                break;
            }
        }

        if ($ga4 && !preg_match('/^G-[A-Z0-9]+$/i', $ga4)) $this->error['warning'] = 'GA4 Measurement ID formatı geçersiz. Örnek: G-XXXXXXXXXX';
        if ($meta && !preg_match('/^[0-9]{5,20}$/', $meta)) $this->error['warning'] = 'Meta Pixel ID formatı geçersiz.';
        if ($ads && !preg_match('/^AW-[0-9]+$/i', $ads)) $this->error['warning'] = 'Google Ads ID formatı geçersiz. Örnek: AW-123456789';
        if ($label && !preg_match('/^[A-Za-z0-9_-]+$/', $label)) $this->error['warning'] = 'Google Ads dönüşüm etiketi formatı geçersiz.';

        return !$this->error;
    }
}
