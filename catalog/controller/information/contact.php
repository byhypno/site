<?php
class ControllerInformationContact extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('information/contact');
		require_once(DIR_SYSTEM . 'library/egeser_contact.php');

		$this->document->setTitle('İletişim | Egeser Prefabrik İzmir');
		$this->document->setDescription('İzmir Kemalpaşa’daki Egeser Prefabrik ile prefabrik ev, kurumsal yapı, proje, üretim, sevkiyat ve montaj talepleriniz için iletişime geçin.');

		// EGESER - canonical contact URL
		$this->document->addLink(rtrim($this->config->get('config_url'), '/') . '/iletisim', 'canonical');

		// EGESER - CSRF token
		if (empty($this->session->data['egeser_contact_csrf'])) {
			$this->session->data['egeser_contact_csrf'] = bin2hex(random_bytes(32));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$name         = trim((string)$this->request->post['name']);
			$telephone    = trim((string)$this->request->post['telephone']);
			$email        = trim((string)$this->request->post['email']);
			$project_type = isset($this->request->post['project_type']) ? trim((string)$this->request->post['project_type']) : '';
			$enquiry      = trim((string)$this->request->post['enquiry']);

			$to = EgeserContact::EMAIL;
			$from = trim((string)$this->config->get('config_mail_smtp_username'));

			if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
				$from = $to;
			}

			$subject_parts = array('Egeser Prefabrik İletişim Formu');
			if ($project_type !== '') {
				$subject_parts[] = $project_type;
			}
			$subject_parts[] = $name;
			$subject = implode(' | ', $subject_parts);

			$text  = "Egeser Prefabrik - İletişim Formu\n";
			$text .= "----------------------------------\n";
			$text .= "Ad Soyad: " . $name . "\n";
			$text .= "Telefon: " . $telephone . "\n";
			$text .= "E-posta: " . $email . "\n";
			$text .= "Talep Türü: " . ($project_type !== '' ? $project_type : '-') . "\n\n";
			$text .= "Mesaj:\n" . $enquiry . "\n";

			$html  = '<h2>Egeser Prefabrik - İletişim Formu</h2>';
			$html .= '<p><strong>Ad Soyad:</strong> ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '</p>';
			$html .= '<p><strong>Telefon:</strong> ' . htmlspecialchars($telephone, ENT_QUOTES, 'UTF-8') . '</p>';
			$html .= '<p><strong>E-posta:</strong> ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</p>';
			$html .= '<p><strong>Talep Türü:</strong> ' . htmlspecialchars($project_type !== '' ? $project_type : '-', ENT_QUOTES, 'UTF-8') . '</p>';
			$html .= '<p><strong>Mesaj:</strong><br>' . nl2br(htmlspecialchars($enquiry, ENT_QUOTES, 'UTF-8')) . '</p>';

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
				$mail->setFrom($from);
				$mail->setSender($name);

				if (method_exists($mail, 'setReplyTo')) {
					$mail->setReplyTo($email);
				}

				$mail->setSubject($subject);
				$mail->setText($text);

				if (method_exists($mail, 'setHtml')) {
					$mail->setHtml($html);
				}

				$mail->send();

				$this->log->write(
					'EGESER CONTACT MAIL OK | to=' . $to .
					' | from=' . $from .
					' | reply-to=' . $email
				);

				// Token tek kullanımlık olsun.
				unset($this->session->data['egeser_contact_csrf']);

				// EGESER Ziyaretci & Lead Takip Merkezi - iletisim formu event'i.
				try {
					require_once(DIR_SYSTEM . 'library/egeser_visitor_tracker.php');
					$egeser_tracker = new EgeserVisitorTracker($this->registry);
					$egeser_tracker->trackClientEvent('contact_form_submit', array(
						'page_url' => rtrim($this->config->get('config_url'), '/') . '/iletisim'
					));
				} catch (Exception $e) {
					$this->log->write('Egeser Visitor Tracker (contact_form_submit) error: ' . $e->getMessage());
				}

				$this->response->redirect($this->url->link('information/contact/success'));
				return;
			} catch (Exception $e) {
				$this->log->write('EGESER CONTACT MAIL ERROR | ' . $e->getMessage());
				$this->error['send'] = 'Mesaj gönderilirken bir sorun oluştu. Lütfen tekrar deneyin veya telefon/WhatsApp üzerinden bize ulaşın.';
			}
		}

		// EGESER sabit iletisim bilgileri (kullanici tarafindan teyitli).
		$data['egeser_whatsapp'] = EgeserContact::WHATSAPP;

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => 'Ana Sayfa',
			'href' => rtrim($this->config->get('config_url'), '/') . '/'
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/contact')
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['egeser_schema_url'] = rtrim($this->config->get('config_url'), '/') . '/iletisim';
		$data['egeser_schema_description'] = 'İzmir Kemalpaşa’daki Egeser Prefabrik ile prefabrik ev, kurumsal yapı, proje, üretim, sevkiyat ve montaj talepleriniz için iletişime geçin.';

		$data['text_location'] = $this->language->get('text_location');
		$data['text_store'] = $this->language->get('text_store');
		$data['text_contact'] = $this->language->get('text_contact');
		$data['text_address'] = $this->language->get('text_address');
		$data['text_telephone'] = $this->language->get('text_telephone');
		$data['text_fax'] = $this->language->get('text_fax');
		$data['text_open'] = $this->language->get('text_open');
		$data['text_comment'] = $this->language->get('text_comment');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_enquiry'] = $this->language->get('entry_enquiry');

		$data['button_map'] = $this->language->get('button_map');
		$data['button_submit'] = $this->language->get('button_submit');

		$data['error_name'] = isset($this->error['name']) ? $this->error['name'] : '';
		$data['error_telephone'] = isset($this->error['telephone']) ? $this->error['telephone'] : '';
		$data['error_email'] = isset($this->error['email']) ? $this->error['email'] : '';
		$data['error_enquiry'] = isset($this->error['enquiry']) ? $this->error['enquiry'] : '';
		$data['error_kvkk'] = isset($this->error['kvkk']) ? $this->error['kvkk'] : '';
		$data['error_csrf'] = isset($this->error['csrf']) ? $this->error['csrf'] : '';
		$data['error_send'] = isset($this->error['send']) ? $this->error['send'] : '';

		$data['csrf_token'] = isset($this->session->data['egeser_contact_csrf'])
			? (string)$this->session->data['egeser_contact_csrf']
			: '';

		$data['action'] = $this->url->link('information/contact', '', true);

		$this->load->model('tool/image');

		if ($this->config->get('config_image')) {
			$data['image'] = $this->model_tool_image->resize(
				$this->config->get('config_image'),
				$this->config->get($this->config->get('config_theme') . '_image_location_width'),
				$this->config->get($this->config->get('config_theme') . '_image_location_height')
			);
		} else {
			$data['image'] = false;
		}

		$data['store'] = $this->config->get('config_name');
		$data['address'] = nl2br($this->config->get('config_address'));
		$data['geocode'] = $this->config->get('config_geocode');
		$data['geocode_hl'] = $this->config->get('config_language');

		// Store email is shown in the contact card.
		$data['email_store'] = EgeserContact::EMAIL;

		// Form values: store phone must NOT prefill the visitor phone field.
		$data['name'] = isset($this->request->post['name'])
			? (string)$this->request->post['name']
			: ($this->customer->isLogged() ? $this->customer->getFirstName() : '');

		$data['telephone'] = isset($this->request->post['telephone'])
			? (string)$this->request->post['telephone']
			: '';

		$data['email'] = isset($this->request->post['email'])
			? (string)$this->request->post['email']
			: ($this->customer->isLogged() ? $this->customer->getEmail() : '');

		$data['project_type'] = isset($this->request->post['project_type'])
			? (string)$this->request->post['project_type']
			: '';

		$data['enquiry'] = isset($this->request->post['enquiry'])
			? (string)$this->request->post['enquiry']
			: '';

		$data['kvkk'] = isset($this->request->post['kvkk']) && (string)$this->request->post['kvkk'] === '1';

		$data['fax'] = $this->config->get('config_fax');
		$data['open'] = nl2br($this->config->get('config_open'));
		$data['comment'] = $this->config->get('config_comment');

		$data['locations'] = array();

		$this->load->model('localisation/location');

		foreach ((array)$this->config->get('config_location') as $location_id) {
			$location_info = $this->model_localisation_location->getLocation($location_id);

			if ($location_info) {
				if ($location_info['image']) {
					$image = $this->model_tool_image->resize(
						$location_info['image'],
						$this->config->get($this->config->get('config_theme') . '_image_location_width'),
						$this->config->get($this->config->get('config_theme') . '_image_location_height')
					);
				} else {
					$image = false;
				}

				$data['locations'][] = array(
					'location_id' => $location_info['location_id'],
					'name'        => $location_info['name'],
					'address'     => nl2br($location_info['address']),
					'geocode'     => $location_info['geocode'],
					'telephone'   => $location_info['telephone'],
					'fax'         => $location_info['fax'],
					'image'       => $image,
					'open'        => nl2br($location_info['open']),
					'comment'     => $location_info['comment']
				);
			}
		}

		// Captcha
		if (
			$this->config->get($this->config->get('config_captcha') . '_status') &&
			in_array('contact', (array)$this->config->get('config_captcha_page'))
		) {
			$data['captcha'] = $this->load->controller(
				'extension/captcha/' . $this->config->get('config_captcha'),
				$this->error
			);
		} else {
			$data['captcha'] = '';
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/contact', $data));
	}

	protected function validate() {
		// Honeypot: bot doldurursa reddet.
		if (!empty($this->request->post['website'])) {
			$this->error['csrf'] = 'Form doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.';
		}

		$expected = isset($this->session->data['egeser_contact_csrf'])
			? (string)$this->session->data['egeser_contact_csrf']
			: '';
		$given = isset($this->request->post['csrf_token'])
			? (string)$this->request->post['csrf_token']
			: '';

		if (!$expected || !$given || !hash_equals($expected, $given)) {
			$this->error['csrf'] = 'Oturum doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.';
		}

		$name = isset($this->request->post['name']) ? trim((string)$this->request->post['name']) : '';
		if ((utf8_strlen($name) < 3) || (utf8_strlen($name) > 80)) {
			$this->error['name'] = 'Ad Soyad 3 ile 80 karakter arasında olmalıdır.';
		}

		$telephone = isset($this->request->post['telephone']) ? trim((string)$this->request->post['telephone']) : '';
		$telephone_digits = preg_replace('/\D+/', '', $telephone);
		if (strlen($telephone_digits) < 10 || strlen($telephone_digits) > 15) {
			$this->error['telephone'] = 'Geçerli bir telefon numarası giriniz.';
		}

		$email = isset($this->request->post['email']) ? trim((string)$this->request->post['email']) : '';
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}

		$project_type = isset($this->request->post['project_type']) ? trim((string)$this->request->post['project_type']) : '';
		$allowed_project_types = array('', 'Bireysel Prefabrik Ev', 'Kurumsal Prefabrik Yapı', 'Teknik Bilgi', 'Diğer');
		if (!in_array($project_type, $allowed_project_types, true)) {
			$this->request->post['project_type'] = '';
		}

		$enquiry = isset($this->request->post['enquiry']) ? trim((string)$this->request->post['enquiry']) : '';
		if ((utf8_strlen($enquiry) < 10) || (utf8_strlen($enquiry) > 3000)) {
			$this->error['enquiry'] = $this->language->get('error_enquiry');
		}

		if (!isset($this->request->post['kvkk']) || (string)$this->request->post['kvkk'] !== '1') {
			$this->error['kvkk'] = 'Devam etmek için iletişim bilgilerinizin işlenmesini kabul etmeniz gerekir.';
		}

		// Captcha
		if (
			$this->config->get($this->config->get('config_captcha') . '_status') &&
			in_array('contact', (array)$this->config->get('config_captcha_page'))
		) {
			$captcha = $this->load->controller(
				'extension/captcha/' . $this->config->get('config_captcha') . '/validate'
			);

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		return !$this->error;
	}

	public function success() {
		$this->load->language('information/contact');

		// EGESER - Form başarı sayfası arama motorlarında indekslenmesin.
		$this->response->addHeader('X-Robots-Tag: noindex, follow');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->addLink(rtrim($this->config->get('config_url'), '/') . '/iletisim', 'canonical');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => '/'
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/contact')
		);

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_message'] = $this->language->get('text_success');
		$data['button_continue'] = $this->language->get('button_continue');
		$data['continue'] = '/';

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}
