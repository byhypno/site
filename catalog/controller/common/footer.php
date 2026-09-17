<?php
class ControllerCommonFooter extends Controller {
	public function index() {
		$this->load->language('common/footer');

		$data['scripts'] = $this->document->getScripts('footer');

		$data['text_information'] = $this->language->get('text_information');
		$data['text_service'] = $this->language->get('text_service');
		$data['text_extra'] = $this->language->get('text_extra');
		$data['text_contact'] = $this->language->get('text_contact');
		$data['text_return'] = $this->language->get('text_return');
		$data['text_sitemap'] = $this->language->get('text_sitemap');
		$data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$data['text_voucher'] = $this->language->get('text_voucher');
		$data['text_affiliate'] = $this->language->get('text_affiliate');
		$data['text_special'] = $this->language->get('text_special');
		$data['text_account'] = $this->language->get('text_account');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_wishlist'] = $this->language->get('text_wishlist');
		$data['text_newsletter'] = $this->language->get('text_newsletter');

		$this->load->model('catalog/information');

		$data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			if ($result['bottom']) {
				$data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
		}

		$data['contact'] = $this->url->link('information/contact');

		/*
		 * EGESER Footer Link Stabilizasyon V1.0
		 * Footer linkleri tek noktadan ve OpenCart URL sinifi uzerinden uretilir.
		 * Eski kategori ID fallback'leri (97, 98, 99, 103-108, 127, 128, 133) kullanilmaz.
		 */
		$data['egeser_url_iletisim'] = $data['contact'];
		$data['egeser_url_prefabrik_yapilar'] = '/prefabrik-ev-modelleri';
		$data['egeser_url_tek_katli'] = $this->url->link('product/category', 'path=60');
		$data['egeser_url_cift_katli'] = $this->url->link('product/category', 'path=61');
		$data['egeser_url_ofis_yonetim'] = $this->url->link('product/category', 'path=62');
		$data['egeser_url_yatakhane'] = $this->url->link('product/category', 'path=63');
		$data['egeser_url_yemekhane'] = $this->url->link('product/category', 'path=64');
		$data['egeser_url_santiye'] = $this->url->link('product/category', 'path=65');
		$data['egeser_url_sosyal_tesis'] = $this->url->link('product/category', 'path=66');
		$data['egeser_url_ozel_proje'] = $this->url->link('product/category', 'path=67');

		$data['egeser_url_hakkimizda'] = $this->url->link('information/information', 'information_id=7');
		$data['egeser_url_referanslar'] = $this->url->link('information/information', 'information_id=9');
		$data['egeser_url_teknik'] = $this->url->link('information/information', 'information_id=10');

		// EGESER sabit iletisim bilgileri (kullanici tarafindan teyitli).
		require_once(DIR_SYSTEM . 'library/egeser_contact.php');
		$data['eg112_phone'] = '0531 886 60 90';
		$data['egeser_whatsapp'] = EgeserContact::WHATSAPP;
		$data['return'] = $this->url->link('account/return/add', '', true);
		$data['sitemap'] = $this->url->link('information/sitemap');
		$data['manufacturer'] = $this->url->link('product/manufacturer');
		$data['voucher'] = $this->url->link('account/voucher', '', true);
		$data['affiliate'] = $this->url->link('affiliate/account', '', true);
		$data['special'] = $this->url->link('product/special');
		$data['account'] = $this->url->link('account/account', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['newsletter'] = $this->url->link('account/newsletter', '', true);

		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		// Whos Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			if (isset($this->request->server['REMOTE_ADDR'])) {
				$ip = $this->request->server['REMOTE_ADDR'];
			} else {
				$ip = '';
			}

			if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
				$url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
			} else {
				$url = '';
			}

			if (isset($this->request->server['HTTP_REFERER'])) {
				$referer = $this->request->server['HTTP_REFERER'];
			} else {
				$referer = '';
			}

			$this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
		}

		return $this->load->view('common/footer', $data);
	}
}
