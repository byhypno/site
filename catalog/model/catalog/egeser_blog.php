<?php
class ModelCatalogEgeserBlog extends Model {
    public function getPosts() {
        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "egeser_blog_post
            WHERE status = '1'
            ORDER BY date_published DESC, sort_order ASC, blog_id DESC
        ");

        return $query->rows;
    }

    public function getPostById($blog_id) {
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
        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "egeser_blog_post
            WHERE slug = '" . $this->db->escape((string)$slug) . "'
              AND status = '1'
            LIMIT 1
        ");

        return $query->num_rows ? $query->row : array();
    }
}
