<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('extension/extension');

		$data['analytics'] = array();

		$analytics = $this->model_extension_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get($analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get($analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['egeser_robots'] = $this->config->get('egeser_robots');
		$data['links'] = $this->document->getLinks();

		// EGESER - Tracking ayarlarini tema katmanina guvenli sekilde aktar.
		// Site Kontrol Merkezi ayarlari (egeser_health_*) asil kaynaktir; eski anahtarlar geriye uyumluluk icin korunur.
		$data['egeser_tracking_status'] = $this->config->has('egeser_health_tracking_status')
			? (bool)$this->config->get('egeser_health_tracking_status')
			: (bool)$this->config->get('egeser_tracking_status');
		$data['egeser_ga4_id'] = trim((string)($this->config->has('egeser_health_ga4_id') ? $this->config->get('egeser_health_ga4_id') : $this->config->get('egeser_ga4_id')));
		$data['egeser_meta_pixel_id'] = trim((string)($this->config->has('egeser_health_meta_pixel_id') ? $this->config->get('egeser_health_meta_pixel_id') : $this->config->get('egeser_meta_pixel_id')));
		$data['egeser_google_ads_id'] = trim((string)($this->config->has('egeser_health_google_ads_id') ? $this->config->get('egeser_health_google_ads_id') : $this->config->get('egeser_google_ads_id')));
		$data['egeser_google_ads_lead_label'] = trim((string)($this->config->has('egeser_health_google_ads_lead_label') ? $this->config->get('egeser_health_google_ads_lead_label') : $this->config->get('egeser_google_ads_lead_label')));

		// EGESER - Open Graph / Twitter URL'si canonical varsa onu kullanir.
		$egeser_social_url = '';
		foreach ($data['links'] as $egeser_link) {
			if (isset($egeser_link['rel']) && strtolower($egeser_link['rel']) === 'canonical' && !empty($egeser_link['href'])) {
				$egeser_social_url = html_entity_decode($egeser_link['href'], ENT_QUOTES, 'UTF-8');
				break;
			}
		}

		if ($egeser_social_url === '') {
			$egeser_request_uri = isset($this->request->server['REQUEST_URI']) ? (string)$this->request->server['REQUEST_URI'] : '/';
			$egeser_request_path = parse_url($egeser_request_uri, PHP_URL_PATH);
			$egeser_social_url = rtrim($server, '/') . '/' . ltrim($egeser_request_path ? $egeser_request_path : '', '/');
		}

		$data['egeser_og_title'] = $data['title'];
		$data['egeser_og_description'] = $data['description'];
		$data['egeser_og_url'] = $egeser_social_url;
		$data['egeser_og_type'] = (isset($this->request->get['route']) && $this->request->get['route'] === 'product/product') ? 'product' : 'website';
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts();
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$data['egeser_og_image'] = $data['logo'];

		$this->load->language('common/header');

		$data['text_home'] = $this->language->get('text_home');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

		$data['text_account'] = $this->language->get('text_account');
		$data['text_register'] = $this->language->get('text_register');
		$data['text_login'] = $this->language->get('text_login');
		$data['text_order'] = $this->language->get('text_order');
		$data['text_transaction'] = $this->language->get('text_transaction');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_logout'] = $this->language->get('text_logout');
		$data['text_checkout'] = $this->language->get('text_checkout');
		$data['text_category'] = $this->language->get('text_category');
		$data['text_all'] = $this->language->get('text_all');

		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		foreach ($categories as $category) {
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					$children_data[] = array(
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
					);
				}

				// Level 1
				$data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}

		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');

		// For page specific css
		if (isset($this->request->get['route'])) {
			if (isset($this->request->get['product_id'])) {
				$class = '-' . $this->request->get['product_id'];
			} elseif (isset($this->request->get['path'])) {
				$class = '-' . $this->request->get['path'];
			} elseif (isset($this->request->get['manufacturer_id'])) {
				$class = '-' . $this->request->get['manufacturer_id'];
			} elseif (isset($this->request->get['information_id'])) {
				$class = '-' . $this->request->get['information_id'];
			} else {
				$class = '';
			}

			$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
		} else {
			$data['class'] = 'common-home';
		}

		return $this->load->view('common/header', $data);
	}
}
