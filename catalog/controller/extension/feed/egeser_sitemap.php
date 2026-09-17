<?php
class ControllerExtensionFeedEgeserSitemap extends Controller {
	public function index() {
		$this->response->addHeader('Content-Type: application/xml; charset=UTF-8');

		$store_id = (int)$this->config->get('config_store_id');
		$urls = array();

		// Ana sayfa
		$this->addUrl($urls, rtrim($this->config->get('config_url'), '/') . '/', '', 'daily', '1.0');

		// Kategoriler
		$category_query = $this->db->query("
			SELECT c.category_id, c.date_modified
			FROM " . DB_PREFIX . "category c
			INNER JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
			WHERE c.status = '1'
			  AND c2s.store_id = '" . $store_id . "'
			ORDER BY c.sort_order ASC, c.category_id ASC
		");

		foreach ($category_query->rows as $row) {
			$url = html_entity_decode(
				$this->url->link('product/category', 'path=' . (int)$row['category_id']),
				ENT_QUOTES,
				'UTF-8'
			);

			if ($this->isSeoUrl($url)) {
				$this->addUrl(
					$urls,
					$url,
					!empty($row['date_modified']) ? $row['date_modified'] : '',
					'weekly',
					'0.8'
				);
			}
		}

		// Ürünler
		$product_query = $this->db->query("
			SELECT p.product_id, p.date_modified
			FROM " . DB_PREFIX . "product p
			INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			WHERE p.status = '1'
			  AND p.date_available <= NOW()
			  AND p2s.store_id = '" . $store_id . "'
			ORDER BY p.product_id ASC
		");

		foreach ($product_query->rows as $row) {
			$url = html_entity_decode(
				$this->url->link('product/product', 'product_id=' . (int)$row['product_id']),
				ENT_QUOTES,
				'UTF-8'
			);

			if ($this->isSeoUrl($url)) {
				$this->addUrl(
					$urls,
					$url,
					!empty($row['date_modified']) ? $row['date_modified'] : '',
					'weekly',
					'0.7'
				);
			}
		}

		// Bilgi sayfaları (Hakkımızda, Projelerimiz, Teknik Bilgiler vb.)
		$information_query = $this->db->query("
			SELECT i.information_id
			FROM " . DB_PREFIX . "information i
			INNER JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id)
			WHERE i.status = '1'
			  AND i2s.store_id = '" . $store_id . "'
			ORDER BY i.sort_order ASC, i.information_id ASC
		");

		foreach ($information_query->rows as $row) {
			$url = html_entity_decode(
				$this->url->link('information/information', 'information_id=' . (int)$row['information_id']),
				ENT_QUOTES,
				'UTF-8'
			);

			if ($this->isSeoUrl($url)) {
				$this->addUrl($urls, $url, '', 'monthly', '0.6');
			}
		}

		// Özel iletişim SEO URL'si
		$this->addUrl(
			$urls,
			rtrim($this->config->get('config_url'), '/') . '/iletisim',
			'',
			'monthly',
			'0.6'
		);

		// Paket 4A - Blog ana sayfası ve aktif blog yazıları
		$blog_base = rtrim($this->config->get('config_url'), '/') . '/blog';
		$this->addUrl($urls, $blog_base, '', 'weekly', '0.7');

		$blog_query = $this->db->query("
			SELECT slug, date_modified
			FROM " . DB_PREFIX . "egeser_blog_post
			WHERE status = '1'
			ORDER BY date_published DESC, blog_id DESC
		");

		foreach ($blog_query->rows as $row) {
			$this->addUrl(
				$urls,
				$blog_base . '/' . ltrim($row['slug'], '/'),
				!empty($row['date_modified']) ? $row['date_modified'] : '',
				'monthly',
				'0.7'
			);
		}

		$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

		foreach ($urls as $item) {
			$xml .= "  <url>\n";
			$xml .= '    <loc>' . htmlspecialchars($item['loc'], ENT_QUOTES, 'UTF-8') . "</loc>\n";

			if ($item['lastmod'] !== '') {
				$timestamp = strtotime($item['lastmod']);

				if ($timestamp) {
					$xml .= '    <lastmod>' . date('Y-m-d', $timestamp) . "</lastmod>\n";
				}
			}

			$xml .= '    <changefreq>' . $item['changefreq'] . "</changefreq>\n";
			$xml .= '    <priority>' . $item['priority'] . "</priority>\n";
			$xml .= "  </url>\n";
		}

		$xml .= "</urlset>\n";

		$this->response->setOutput($xml);
	}

	private function addUrl(&$urls, $loc, $lastmod, $changefreq, $priority) {
		$loc = trim((string)$loc);

		if ($loc === '') {
			return;
		}

		$key = strtolower($loc);

		if (!isset($urls[$key])) {
			$urls[$key] = array(
				'loc' => $loc,
				'lastmod' => $lastmod,
				'changefreq' => $changefreq,
				'priority' => $priority
			);
		}
	}

	private function isSeoUrl($url) {
		$url = (string)$url;

		if ($url === '') {
			return false;
		}

		// Sitemap'e yalnız temiz SEO URL'leri al.
		if (strpos($url, 'index.php?route=') !== false) {
			return false;
		}

		return true;
	}
}
