<?php
class ModelCatalogEgeserBlog extends Model {
    private function ensureViewsColumn() {
        static $checked = false;

        if ($checked) {
            return;
        }

        $checked = true;

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "egeser_blog_post` LIKE 'views'");

        if (!$query->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "egeser_blog_post` ADD COLUMN `views` INT UNSIGNED NOT NULL DEFAULT 0");
        }
    }

    public function getPosts() {
        $this->ensureViewsColumn();

        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "egeser_blog_post
            WHERE status = '1'
            ORDER BY date_published DESC, sort_order ASC, blog_id DESC
        ");

        return $query->rows;
    }

    public function getPostById($blog_id) {
        $this->ensureViewsColumn();

        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "egeser_blog_post
            WHERE blog_id = '" . (int)$blog_id . "'
              AND status = '1'
            LIMIT 1
        ");

        return $query->num_rows ? $query->row : array();
    }

    public function getPostBySlug($slug) {
        $this->ensureViewsColumn();

        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "egeser_blog_post
            WHERE slug = '" . $this->db->escape((string)$slug) . "'
              AND status = '1'
            LIMIT 1
        ");

        return $query->num_rows ? $query->row : array();
    }

    public function incrementViews($blog_id) {
        $this->ensureViewsColumn();

        $this->db->query("UPDATE `" . DB_PREFIX . "egeser_blog_post` SET views = views + 1 WHERE blog_id = '" . (int)$blog_id . "'");
    }
}
