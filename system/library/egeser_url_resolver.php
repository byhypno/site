<?php
class EgeserUrlResolver {
    private $registry;
    private $db;
    private $url;

    public function __construct($registry) {
        $this->registry = $registry;
        $this->db = $registry->get('db');
        $this->url = $registry->get('url');
    }

    public function routeByKeyword($keyword, $fallback = '#') {
        $keyword = trim((string)$keyword);
        if ($keyword === '') return $fallback;

        $q = $this->db->query(
            "SELECT query FROM " . DB_PREFIX . "url_alias
             WHERE keyword = '" . $this->db->escape($keyword) . "'
             ORDER BY url_alias_id ASC LIMIT 1"
        );

        if (!$q->num_rows || empty($q->row['query'])) return $fallback;

        $query = (string)$q->row['query'];

        if (strpos($query, 'category_id=') === 0) {
            $id = (int)substr($query, 12);
            return $id > 0 ? $this->url->link('product/category', 'path=' . $id, true) : $fallback;
        }

        if (strpos($query, 'information_id=') === 0) {
            $id = (int)substr($query, 15);
            return $id > 0 ? $this->url->link('information/information', 'information_id=' . $id, true) : $fallback;
        }

        if (strpos($query, 'product_id=') === 0) {
            $id = (int)substr($query, 11);
            return $id > 0 ? $this->url->link('product/product', 'product_id=' . $id, true) : $fallback;
        }

        return $fallback;
    }
}
