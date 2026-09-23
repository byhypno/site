<?php
class ModelCatalogEgeserBlog extends Model {
    private function ensureColumns() {
        static $checked = false;

        if ($checked) {
            return;
        }

        $checked = true;

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "egeser_blog_post` LIKE 'views'");

        if (!$query->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "egeser_blog_post` ADD COLUMN `views` INT UNSIGNED NOT NULL DEFAULT 0");
        }

        $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "egeser_blog_post` LIKE 'tags'");

        if (!$query->num_rows) {
            $this->db->query("ALTER TABLE `" . DB_PREFIX . "egeser_blog_post` ADD COLUMN `tags` VARCHAR(255) NOT NULL DEFAULT ''");
        }
    }

    public function getPosts() {
        $this->ensureColumns();

        $query = $this->db->query("
            SELECT *
            FROM " . DB_PREFIX . "egeser_blog_post
            WHERE status = '1'
            ORDER BY date_published DESC, sort_order ASC, blog_id DESC
        ");

        return $query->rows;
    }

    public function getPostById($blog_id) {
        $this->ensureColumns();

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
        $this->ensureColumns();

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
        $this->ensureColumns();

        $this->db->query("UPDATE `" . DB_PREFIX . "egeser_blog_post` SET views = views + 1 WHERE blog_id = '" . (int)$blog_id . "'");
    }

    public function getRelatedPosts($exclude_blog_id, $tags = '', $limit = 3) {
        $this->ensureColumns();

        $related = array();
        $used_ids = array((int)$exclude_blog_id);

        $tag_list = array_filter(array_map('trim', explode(',', (string)$tags)));

        if ($tag_list) {
            $tag_conditions = array();

            foreach ($tag_list as $tag) {
                $tag_conditions[] = "tags LIKE '%" . $this->db->escape($tag) . "%'";
            }

            $query = $this->db->query("
                SELECT *
                FROM " . DB_PREFIX . "egeser_blog_post
                WHERE status = '1'
                  AND blog_id NOT IN (" . implode(',', $used_ids) . ")
                  AND (" . implode(' OR ', $tag_conditions) . ")
                ORDER BY date_published DESC, sort_order ASC, blog_id DESC
                LIMIT " . (int)$limit . "
            ");

            $related = $query->rows;

            foreach ($related as $row) {
                $used_ids[] = (int)$row['blog_id'];
            }
        }

        if (count($related) < $limit) {
            $query = $this->db->query("
                SELECT *
                FROM " . DB_PREFIX . "egeser_blog_post
                WHERE status = '1'
                  AND blog_id NOT IN (" . implode(',', $used_ids) . ")
                ORDER BY date_published DESC, sort_order ASC, blog_id DESC
                LIMIT " . (int)($limit - count($related)) . "
            ");

            $related = array_merge($related, $query->rows);
        }

        return $related;
    }
}
