<?php
class ControllerExtensionModuleEgeserLead extends Controller {
    public function nonce() {
        $this->response->addHeader('X-Robots-Tag: noindex, nofollow');
        $this->response->addHeader('X-Content-Type-Options: nosniff');
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->response->addHeader('Pragma: no-cache');

        if ($this->request->server['REQUEST_METHOD'] !== 'GET') {
            $this->response->addHeader('HTTP/1.1 405 Method Not Allowed');
            $this->response->setOutput(json_encode(array('success'=>false,'message'=>'Geçersiz istek.')));
            return;
        }

        $xhr = isset($this->request->server['HTTP_X_REQUESTED_WITH']) ? strtolower((string)$this->request->server['HTTP_X_REQUESTED_WITH']) : '';
        if ($xhr !== 'xmlhttprequest') {
            $this->response->addHeader('HTTP/1.1 403 Forbidden');
            $this->response->setOutput(json_encode(array('success'=>false,'message'=>'Geçersiz istek kaynağı.')));
            return;
        }

        $this->load->model('extension/module/egeser_lead_security_final');
        $this->response->setOutput(json_encode(array(
            'success'=>true,
            'csrf_token'=>$this->model_extension_module_egeser_lead_security_final->issueNonce()
        )));
    }

    public function submit() {
        $this->response->addHeader('X-Robots-Tag: noindex, nofollow');
        $this->response->addHeader('X-Content-Type-Options: nosniff');
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->addHeader('Cache-Control: no-store, no-cache, must-revalidate');

        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $this->response->addHeader('HTTP/1.1 405 Method Not Allowed');
            $this->response->setOutput(json_encode(array('success'=>false,'message'=>'Geçersiz istek.')));
            return;
        }

        $this->load->model('extension/module/egeser_lead');
        $this->model_extension_module_egeser_lead->install();
        $this->load->model('extension/module/egeser_lead_security_final');

        if (!$this->model_extension_module_egeser_lead->rateLimitAllowed(5, 600)) {
            $this->response->addHeader('HTTP/1.1 429 Too Many Requests');
            $this->response->addHeader('Retry-After: 600');
            $this->response->setOutput(json_encode(array('success'=>false,'message'=>'Çok fazla teklif isteği gönderildi. Lütfen daha sonra tekrar deneyin.')));
            return;
        }

        if (!empty($this->request->post['website'])) {
            $this->response->setOutput(json_encode(array('success'=>true,'message'=>'Talebiniz alındı.')));
            return;
        }

        $given = isset($this->request->post['csrf_token']) ? trim((string)$this->request->post['csrf_token']) : '';

        if (!$given || !$this->model_extension_module_egeser_lead_security_final->verifyNonce($given)) {
            $this->response->addHeader('HTTP/1.1 403 Forbidden');
            $this->response->setOutput(json_encode(array(
                'success'=>false,
                'csrf_refresh'=>true,
                'message'=>'Form güvenlik doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.'
            )));
            return;
        }

        $customer_type = $this->clean(isset($this->request->post['customer_type']) ? $this->request->post['customer_type'] : '');
        $company = $this->clean(isset($this->request->post['company']) ? $this->request->post['company'] : '');
        $name = $this->clean(isset($this->request->post['name']) ? $this->request->post['name'] : '');
        $phone = $this->clean(isset($this->request->post['phone']) ? $this->request->post['phone'] : '');
        $email = trim(isset($this->request->post['email']) ? (string)$this->request->post['email'] : '');
        $project_type = $this->clean(isset($this->request->post['project_type']) ? $this->request->post['project_type'] : '');
        $location = $this->clean(isset($this->request->post['location']) ? $this->request->post['location'] : '');
        $area = $this->clean(isset($this->request->post['area']) ? $this->request->post['area'] : '');
        $message = $this->cleanMultiline(isset($this->request->post['message']) ? $this->request->post['message'] : '');
        $source = $this->clean(isset($this->request->post['source']) ? $this->request->post['source'] : '');
        $page_url = $this->safeUrl(isset($this->request->post['page_url']) ? $this->request->post['page_url'] : '');
        $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;
        $product_name = $this->clean(isset($this->request->post['product_name']) ? $this->request->post['product_name'] : '');
        $consent = !empty($this->request->post['consent']) ? 1 : 0;

        $customer_type = ($customer_type === 'Kurumsal') ? 'Kurumsal' : 'Bireysel';

        $errors = array();
        if (utf8_strlen($name) < 2 || utf8_strlen($name) > 80) $errors[] = 'Ad Soyad alanını kontrol edin.';
        $digits = preg_replace('/\D+/', '', $phone);
        if (strlen($digits) < 10 || strlen($digits) > 15) $errors[] = 'Telefon numarasını kontrol edin.';
        if (!$consent) $errors[] = 'İletişim onayı zorunludur.';

        if ($customer_type === 'Kurumsal') {
            if (utf8_strlen($company) < 2 || utf8_strlen($company) > 150) $errors[] = 'Firma ünvanını girin.';
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL) || utf8_strlen($email) > 120) $errors[] = 'Geçerli kurumsal e-posta adresini girin.';
        } else {
            $company = '';
            $email = '';
        }

        if ($errors) {
            $this->response->setOutput(json_encode(array('success'=>false,'message'=>implode(' ', $errors))));
            return;
        }

        $utm = array(
            'utm_source'=>$this->clean(isset($this->request->post['utm_source']) ? $this->request->post['utm_source'] : ''),
            'utm_medium'=>$this->clean(isset($this->request->post['utm_medium']) ? $this->request->post['utm_medium'] : ''),
            'utm_campaign'=>$this->clean(isset($this->request->post['utm_campaign']) ? $this->request->post['utm_campaign'] : '')
        );

        if (!$this->model_extension_module_egeser_lead_security_final->consumeNonce($given)) {
            $this->response->addHeader('HTTP/1.1 403 Forbidden');
            $this->response->setOutput(json_encode(array(
                'success'=>false,
                'csrf_refresh'=>true,
                'message'=>'Form güvenlik anahtarı kullanılmış veya süresi dolmuş. Lütfen tekrar deneyin.'
            )));
            return;
        }

        $lead_id = $this->model_extension_module_egeser_lead->addLead(array(
            'customer_type'=>$customer_type,
            'company'=>$company,
            'name'=>$name,
            'phone'=>$phone,
            'email'=>$email,
            'project_type'=>$project_type,
            'location'=>$location,
            'area'=>$area,
            'product_id'=>$product_id,
            'product_name'=>$product_name,
            'message'=>$message,
            'source'=>$source ?: $page_url,
            'page_url'=>$page_url,
            'utm_source'=>$utm['utm_source'],
            'utm_medium'=>$utm['utm_medium'],
            'utm_campaign'=>$utm['utm_campaign'],
            'consent'=>$consent
        ));

        $recipient = $this->config->get('egeser_health_lead_recipient') ? $this->config->get('egeser_health_lead_recipient') : $this->config->get('config_email');
        $subject_prefix = ($customer_type === 'Kurumsal') ? '[KURUMSAL TEKLİF]' : '[BİREYSEL TEKLİF]';
        $subject = $subject_prefix . ' ' . ($product_name ? $product_name : ($project_type ? $project_type : 'Web Sitesi'));
        if ($customer_type === 'Kurumsal' && $company) $subject .= ' - ' . $company;

        $body = array(
            'Lead ID: ' . $lead_id,
            'Müşteri Tipi: ' . $customer_type,
            'Firma: ' . ($company ?: '-'),
            'Ad Soyad: ' . $name,
            'Telefon: ' . $phone,
            'E-posta: ' . ($email ?: '-'),
            'Yapı Türü: ' . ($project_type ?: '-'),
            'Kurulum / Proje Yeri: ' . ($location ?: '-'),
            'Yaklaşık m²: ' . ($area ?: '-'),
            'Ürün ID: ' . ($product_id ?: '-'),
            'Ürün: ' . ($product_name ?: '-'),
            'Kaynak: ' . ($source ?: '-'),
            'Sayfa: ' . ($page_url ?: '-'),
            'UTM Source: ' . ($utm['utm_source'] ?: '-'),
            'UTM Medium: ' . ($utm['utm_medium'] ?: '-'),
            'UTM Campaign: ' . ($utm['utm_campaign'] ?: '-'),
            'Mesaj: ' . ($message ?: '-')
        );

        try {
            $mail = new Mail();
            $mail->protocol = $this->config->get('config_mail_protocol');
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

            $mail->setTo($recipient);

            // SMTP authenticated account must also be used as the envelope/from address.
            // Using config_email (e.g. Gmail) here can fail SPF/DMARC alignment even when SMTP login succeeds.
            $smtp_from = trim((string)$this->config->get('config_mail_smtp_username'));
            if (!$smtp_from || !filter_var($smtp_from, FILTER_VALIDATE_EMAIL)) {
                $smtp_from = trim((string)$this->config->get('config_email'));
            }

            $mail->setFrom($smtp_from);
            $mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

            // Corporate submissions may include a valid customer email; replies should go to the customer.
            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $mail->setReplyTo($email);
            }
            $mail->setSubject($subject);
            $mail->setText(implode("\n", $body));
            $mail->send();

            $this->model_extension_module_egeser_lead->updateMailStatus($lead_id, 'sent', '');
        } catch (Exception $e) {
            $this->model_extension_module_egeser_lead->updateMailStatus($lead_id, 'failed', utf8_substr($e->getMessage(), 0, 500));
            $this->log->write('Egeser Lead Mail Error #' . $lead_id . ': ' . $e->getMessage());
            $this->response->setOutput(json_encode(array(
                'success'=>true,
                'mail_warning'=>true,
                'csrf_token'=>$this->model_extension_module_egeser_lead_security_final->issueNonce(),
                'message'=>'Talebiniz kaydedildi. E-posta bildirimi gecikmiş olabilir; satış ekibi kaydı panelden görebilir.'
            )));
            return;
        }

        $this->response->setOutput(json_encode(array(
            'success'=>true,
            'csrf_token'=>$this->model_extension_module_egeser_lead_security_final->issueNonce(),
            'message'=>'Talebiniz alındı. En kısa sürede sizinle iletişime geçeceğiz.'
        )));
    }

    private function clean($value) {
        $value = html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8');
        $value = strip_tags($value);
        $value = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $value);
        return trim(preg_replace('/\s+/u', ' ', $value));
    }

    private function cleanMultiline($value) {
        $value = html_entity_decode((string)$value, ENT_QUOTES, 'UTF-8');
        $value = strip_tags($value);
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
        return trim($value);
    }

    private function safeUrl($url) {
        $url = trim((string)$url);
        if (!$url) return '';
        $parts = parse_url($url);
        if (!$parts || empty($parts['scheme']) || empty($parts['host'])) return '';
        $base = $this->config->get('config_ssl') ? $this->config->get('config_ssl') : $this->config->get('config_url');
        $base_host = strtolower((string)parse_url($base, PHP_URL_HOST));
        $host = strtolower((string)$parts['host']);
        if ($host !== $base_host && preg_replace('/^www\./','',$host) !== preg_replace('/^www\./','',$base_host)) return '';
        return utf8_substr($url, 0, 700);
    }

    private function secureEquals($known, $user) {
        if (function_exists('hash_equals')) return hash_equals($known, $user);
        if (!is_string($known) || !is_string($user) || strlen($known) !== strlen($user)) return false;
        $result = 0;
        for ($i = 0, $length = strlen($known); $i < $length; $i++) {
            $result |= ord($known[$i]) ^ ord($user[$i]);
        }
        return $result === 0;
    }

    private function token() {
        if (function_exists('random_bytes')) return bin2hex(random_bytes(24));
        return sha1(uniqid(mt_rand(), true)) . sha1(uniqid(mt_rand(), true));
    }
}
