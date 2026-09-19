<?php
class ControllerStartupSeoUrl extends Controller {
	public function index() {
		/*
		 * EGESER - STAGING indeks güvenliği
		 * Sadece egeprefabrikyapi.xyz üzerinde arama motoru indekslemesini kapatır.
		 * Canlı domain egeserprefabrik.com.tr olduğunda bu header otomatik uygulanmaz.
		 */
		$egeser_host = isset($this->request->server['HTTP_HOST']) ? strtolower((string)$this->request->server['HTTP_HOST']) : '';
		$egeser_host = preg_replace('/:\\d+$/', '', $egeser_host);

		if ($egeser_host === 'egeprefabrikyapi.xyz' || $egeser_host === 'www.egeprefabrikyapi.xyz') {
			$this->response->addHeader('X-Robots-Tag: noindex, nofollow');
		}

		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}


		/*
		 * EGESER Paket 4B - Merkezi 301 yönlendirme motoru.
		 * Eski URL'leri yeni veritabanı ID'lerinden bağımsız yakalar.
		 * Pasif + block_disabled kuralı, eski ID'nin yeni sitede başka bir içeriğe
		 * yanlışlıkla çözülmesini önleyerek gerçek 404'e düşürür.
		 */
		if ($this->config->get('egeser_redirect_manager_status') &&
			$this->egeserRedirectTableReady() &&
			isset($this->request->server['REQUEST_METHOD']) &&
			in_array(strtoupper($this->request->server['REQUEST_METHOD']), array('GET','HEAD'), true)) {

			$egeser_redirect_uri = isset($this->request->server['REQUEST_URI']) ? (string)$this->request->server['REQUEST_URI'] : '/';
			$egeser_redirect_source = $this->egeserNormalizeRedirectSource($egeser_redirect_uri);
			$egeser_redirect_rule = false;

			if ($egeser_redirect_source !== '' && $egeser_redirect_source !== '/') {
				$egeser_redirect_hash = sha1($egeser_redirect_source);
				$egeser_redirect_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "egeser_redirect WHERE source_hash='" . $this->db->escape($egeser_redirect_hash) . "' LIMIT 1");
				if ($egeser_redirect_query->num_rows) {
					$egeser_redirect_rule = $egeser_redirect_query->row;
				} else {
					// UTM/gclid/fbclid gibi pazarlama parametreleri ana eski URL kuralını bozmasın.
					$egeser_redirect_clean = $this->egeserNormalizeRedirectSource($egeser_redirect_uri, true);
					if ($egeser_redirect_clean !== $egeser_redirect_source && $egeser_redirect_clean !== '' && $egeser_redirect_clean !== '/') {
						$egeser_redirect_hash = sha1($egeser_redirect_clean);
						$egeser_redirect_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "egeser_redirect WHERE source_hash='" . $this->db->escape($egeser_redirect_hash) . "' LIMIT 1");
						if ($egeser_redirect_query->num_rows) $egeser_redirect_rule = $egeser_redirect_query->row;
					}
				}
			}

			if ($egeser_redirect_rule) {
				if ((int)$egeser_redirect_rule['status'] === 1) {
					$egeser_redirect_target = $this->egeserNormalizeRedirectTarget($egeser_redirect_rule['target_url']);
					if ($egeser_redirect_target !== '' && $egeser_redirect_target !== $egeser_redirect_source) {
						$this->db->query("UPDATE " . DB_PREFIX . "egeser_redirect SET hits=hits+1, last_hit=NOW() WHERE redirect_id='" . (int)$egeser_redirect_rule['redirect_id'] . "'");
						$egeser_redirect_base = rtrim($this->config->get('config_url'), '/');
						$this->response->redirect($egeser_redirect_base . $egeser_redirect_target, 301);
						return;
					}
				} elseif ((int)$egeser_redirect_rule['block_disabled'] === 1) {
					$this->request->get['route'] = 'error/not_found';
					unset($this->request->get['_route_']);
					return;
				}
			}
		}

		/*
		 * EGESER - SEO URL trailing slash temizliği
		 * /hakkimizda/ -> 301 /hakkimizda
		 * Sadece SEO-friendly GET/HEAD isteklerinde çalışır; ana sayfa "/" hariçtir.
		 */
		if ($this->config->get('config_seo_url') &&
			isset($this->request->get['_route_']) &&
			isset($this->request->server['REQUEST_METHOD']) &&
			in_array(strtoupper($this->request->server['REQUEST_METHOD']), array('GET', 'HEAD'), true)) {

			$egeser_request_uri = isset($this->request->server['REQUEST_URI']) ? (string)$this->request->server['REQUEST_URI'] : '';
			$egeser_request_path = parse_url($egeser_request_uri, PHP_URL_PATH);

			if ($egeser_request_path &&
				$egeser_request_path !== '/' &&
				substr($egeser_request_path, -1) === '/') {

				$egeser_clean_path = rtrim($egeser_request_path, '/');
				$egeser_base_url = rtrim($this->config->get('config_url'), '/');
				$egeser_target = $egeser_base_url . '/' . ltrim($egeser_clean_path, '/');

				$egeser_visible_query = parse_url($egeser_request_uri, PHP_URL_QUERY);
				if ($egeser_visible_query !== null && $egeser_visible_query !== '') {
					$egeser_target .= '?' . $egeser_visible_query;
				}

				$this->response->redirect($egeser_target, 301);
				return;
			}
		}

		/*
		 * EGESER - Ana sayfa canonical stabilizasyonu
		 * /index.php?route=common/home -> 301 /
		 */
		if (!isset($this->request->get['_route_']) && isset($this->request->get['route']) && $this->request->get['route'] === 'common/home') {
			$base_url = $this->config->get('config_url');
			$this->response->redirect(rtrim($base_url, '/') . '/', 301);
			return;
		}

		/*
		 * EGESER - Eski OpenCart About Us URL'sini yeni Hakkımızda sayfasına tek adımda yönlendir.
		 * /about_us -> /hakkimizda
		 * REQUEST_URI üzerinden kontrol edilir; _route_ oluşturulma sırasına bağlı değildir.
		 */
		$egeser_about_request_uri = isset($this->request->server['REQUEST_URI']) ? (string)$this->request->server['REQUEST_URI'] : '';
		$egeser_about_request_path = parse_url($egeser_about_request_uri, PHP_URL_PATH);
		$egeser_about_method = isset($this->request->server['REQUEST_METHOD']) ? strtoupper((string)$this->request->server['REQUEST_METHOD']) : 'GET';

		if (in_array($egeser_about_method, array('GET', 'HEAD'), true) &&
			$egeser_about_request_path &&
			rtrim($egeser_about_request_path, '/') === '/about_us') {
			$base_url = $this->config->get('config_url');
			$this->response->redirect(rtrim($base_url, '/') . '/hakkimizda', 301);
			return;
		}

		/*
		 * EGESER - Bilgi sayfalarının klasik OpenCart URL'lerini SEO URL'ye yönlendir.
		 * Örn: /index.php?route=information/information&information_id=9 -> /projelerimiz
		 */
		if (!isset($this->request->get['_route_']) &&
			isset($this->request->get['route']) &&
			$this->request->get['route'] === 'information/information' &&
			isset($this->request->get['information_id']) &&
			(int)$this->request->get['information_id'] > 0 &&
			isset($this->request->server['REQUEST_METHOD']) &&
			in_array(strtoupper($this->request->server['REQUEST_METHOD']), array('GET', 'HEAD'), true)) {

			$egeser_raw_query = isset($this->request->server['QUERY_STRING']) ? html_entity_decode($this->request->server['QUERY_STRING'], ENT_QUOTES, 'UTF-8') : '';
			$egeser_qs = array();
			parse_str($egeser_raw_query, $egeser_qs);

			$egeser_allowed = array('route', 'information_id');
			$egeser_safe = true;

			foreach (array_keys($egeser_qs) as $egeser_key) {
				if (!in_array($egeser_key, $egeser_allowed, true)) {
					$egeser_safe = false;
					break;
				}
			}

			if ($egeser_safe) {
				$egeser_information_id = (int)$this->request->get['information_id'];

				// Eski About Us (information_id=4) doğrudan yeni Hakkımızda URL'sine gider.
				if ($egeser_information_id === 4) {
					$base_url = $this->config->get('config_url');
					$this->response->redirect(rtrim($base_url, '/') . '/hakkimizda', 301);
					return;
				}

				$egeser_alias = $this->db->query("SELECT `keyword` FROM " . DB_PREFIX . "url_alias WHERE `query` = 'information_id=" . $egeser_information_id . "' LIMIT 1");

				if ($egeser_alias->num_rows && !empty($egeser_alias->row['keyword'])) {
					$base_url = $this->config->get('config_url');
					$target = rtrim($base_url, '/') . '/' . ltrim($egeser_alias->row['keyword'], '/');
					$this->response->redirect($target, 301);
					return;
				}
			}
		}

		/*
		 * EGESER - Iletisim URL stabilizasyonu
		 * /index.php?route=information/contact -> 301 /iletisim
		 * /iletisim -> information/contact
		 */
		if (!isset($this->request->get['_route_']) && isset($this->request->get['route']) && $this->request->get['route'] === 'information/contact') {
			$base_url = $this->config->get('config_url');
			$this->response->redirect(rtrim($base_url, '/') . '/iletisim', 301);
			return;
		}

		// Decode URL
		if (isset($this->request->get['_route_'])) {
			$egeser_route = trim($this->request->get['_route_'], '/');

			// EGESER - Temiz sitemap URL
			// /sitemap.xml -> extension/feed/egeser_sitemap
			if ($egeser_route === 'sitemap.xml') {
				$this->request->get['route'] = 'extension/feed/egeser_sitemap';
				return;
			}

			if ($egeser_route === 'iletisim') {
				$this->request->get['route'] = 'information/contact';
				return;
			}

			// EGESER V7 - Şehir bazlı hizmet sayfaları.
			// Veritabanı url_alias kaydına ihtiyaç duymadan temiz ve sabit URL üretir.
			$egeser_city_routes = array(
				'izmir-prefabrik-ev' => 'izmir',
				'tr/izmir-prefabrik-ev' => 'izmir',
				'manisa-prefabrik-ev' => 'manisa',
				'tr/manisa-prefabrik-ev' => 'manisa',
				'aydin-prefabrik-ev' => 'aydin',
				'tr/aydin-prefabrik-ev' => 'aydin',
				'usak-prefabrik-ev' => 'usak',
				'tr/usak-prefabrik-ev' => 'usak',
				'balikesir-prefabrik-ev' => 'balikesir',
				'tr/balikesir-prefabrik-ev' => 'balikesir',
				'mugla-prefabrik-ev' => 'mugla',
				'tr/mugla-prefabrik-ev' => 'mugla'
			);

			if (isset($egeser_city_routes[$egeser_route])) {
				$this->request->get['route'] = 'information/egeser_city';
				$this->request->get['city'] = $egeser_city_routes[$egeser_route];
				return;
			}

			// EGESER Paket 4A - Blog temiz URL'leri
			// /blog -> blog listesi
			// /blog/{slug} -> eski siteden korunan blog yazısı
			if ($egeser_route === 'blog') {
				$this->request->get['route'] = 'information/egeser_blog';
				return;
			}

			if (strpos($egeser_route, 'blog/') === 0) {
				$egeser_blog_slug = substr($egeser_route, 5);

				if ($egeser_blog_slug !== '' && strpos($egeser_blog_slug, '/') === false) {
					$egeser_blog_query = $this->db->query("SELECT blog_id FROM " . DB_PREFIX . "egeser_blog_post WHERE slug = '" . $this->db->escape($egeser_blog_slug) . "' AND status = '1' LIMIT 1");

					if ($egeser_blog_query->num_rows) {
						$this->request->get['route'] = 'information/egeser_blog';
						$this->request->get['blog_id'] = (int)$egeser_blog_query->row['blog_id'];
						return;
					}
				}

				$this->request->get['route'] = 'error/not_found';
				return;
			}

			$parts = explode('/', $this->request->get['_route_']);

			// remove any empty arrays from trailing
			if (utf8_strlen(end($parts)) == 0) {
				array_pop($parts);
			}

			foreach ($parts as $part) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "'");

				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);

					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}

					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}

					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}

					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}

					if ($query->row['query'] && $url[0] != 'information_id' && $url[0] != 'manufacturer_id' && $url[0] != 'category_id' && $url[0] != 'product_id') {
						$this->request->get['route'] = $query->row['query'];
					}
				} else {
					$this->request->get['route'] = 'error/not_found';

					break;
				}
			}

			if (!isset($this->request->get['route'])) {
				if (isset($this->request->get['product_id'])) {
					$this->request->get['route'] = 'product/product';
				} elseif (isset($this->request->get['path'])) {
					$this->request->get['route'] = 'product/category';
				} elseif (isset($this->request->get['manufacturer_id'])) {
					$this->request->get['route'] = 'product/manufacturer/info';
				} elseif (isset($this->request->get['information_id'])) {
					$this->request->get['route'] = 'information/information';
				}
			}
		}
	}


	private function egeserRedirectTableReady() {
		static $ready = null;
		if ($ready !== null) return $ready;
		$table = DB_PREFIX . 'egeser_redirect';
		$q = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape($table) . "'");
		$ready = ($q->num_rows > 0);
		return $ready;
	}


	private function egeserNormalizeRedirectSource($uri, $strip_tracking = false) {
		$uri = html_entity_decode(trim((string)$uri), ENT_QUOTES, 'UTF-8');
		$parts = @parse_url($uri);
		if ($parts === false) return '';
		$path = isset($parts['path']) && $parts['path'] !== '' ? $parts['path'] : '/';
		$path = preg_replace('#/+#', '/', $path);
		if ($path !== '/') $path = rtrim($path, '/');
		if ($path === '') $path = '/';
		$query = array();
		if (!empty($parts['query'])) parse_str($parts['query'], $query);
		if ($strip_tracking && $query) {
			foreach (array_keys($query) as $key) {
				$low = strtolower((string)$key);
				if (strpos($low, 'utm_') === 0 || in_array($low, array('gclid','fbclid','msclkid'), true)) unset($query[$key]);
			}
		}
		$this->egeserKsortRecursive($query);
		$qs = $query ? http_build_query($query, '', '&', PHP_QUERY_RFC3986) : '';
		$qs = str_ireplace('%2F', '/', $qs);
		return $path . ($qs !== '' ? '?' . $qs : '');
	}

	private function egeserNormalizeRedirectTarget($url) {
		$url = trim((string)$url);
		if ($url === '' || preg_match('#^[a-z][a-z0-9+.-]*://#i', $url) || strpos($url, '//') === 0) return '';
		if ($url[0] !== '/') $url = '/' . $url;
		$parts = @parse_url($url);
		if ($parts === false || isset($parts['scheme']) || isset($parts['host']) || isset($parts['fragment'])) return '';
		$path = isset($parts['path']) ? preg_replace('#/+#', '/', $parts['path']) : '/';
		if (strpos($path, '..') !== false) return '';
		if ($path !== '/') $path = rtrim($path, '/');
		$query = array();
		if (!empty($parts['query'])) parse_str($parts['query'], $query);
		$this->egeserKsortRecursive($query);
		$qs = $query ? http_build_query($query, '', '&', PHP_QUERY_RFC3986) : '';
		$qs = str_ireplace('%2F', '/', $qs);
		return $path . ($qs !== '' ? '?' . $qs : '');
	}

	private function egeserKsortRecursive(&$array) {
		if (!is_array($array)) return;
		ksort($array);
		foreach ($array as &$value) if (is_array($value)) $this->egeserKsortRecursive($value);
	}

	public function rewrite($link) {
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

		/*
		 * EGESER - information/contact linklerini SEO URL olarak üret.
		 */
		if (isset($data['route']) && $data['route'] === 'information/contact') {
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . '/iletisim' . $query;
		}

		foreach ($data as $key => $value) {
			if (isset($data['route'])) {
				if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				} elseif ($key == 'path') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int)$category . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				}
			}
		}

		if ($url) {
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
			return $link;
		}
	}
}
