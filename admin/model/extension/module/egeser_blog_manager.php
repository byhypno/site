<?php
class ModelExtensionModuleEgeserBlogManager extends Model {
    public function addBlog($data) {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "egeser_blog_post` SET
            `slug` = '" . $this->db->escape($data['slug']) . "',
            `title` = '" . $this->db->escape($data['title']) . "',
            `description` = '" . $this->db->escape($data['description']) . "',
            `meta_title` = '" . $this->db->escape($data['meta_title']) . "',
            `meta_description` = '" . $this->db->escape($data['meta_description']) . "',
            `meta_keyword` = '" . $this->db->escape($data['meta_keyword']) . "',
            `image` = '" . $this->db->escape($data['image']) . "',
            `status` = '" . (int)$data['status'] . "',
            `sort_order` = '" . (int)$data['sort_order'] . "',
            `date_published` = '" . $this->db->escape($data['date_published']) . "',
            `date_modified` = NOW()");

        return $this->db->getLastId();
    }

    public function editBlog($blog_id, $data) {
        $this->db->query("UPDATE `" . DB_PREFIX . "egeser_blog_post` SET
            `slug` = '" . $this->db->escape($data['slug']) . "',
            `title` = '" . $this->db->escape($data['title']) . "',
            `description` = '" . $this->db->escape($data['description']) . "',
            `meta_title` = '" . $this->db->escape($data['meta_title']) . "',
            `meta_description` = '" . $this->db->escape($data['meta_description']) . "',
            `meta_keyword` = '" . $this->db->escape($data['meta_keyword']) . "',
            `image` = '" . $this->db->escape($data['image']) . "',
            `status` = '" . (int)$data['status'] . "',
            `sort_order` = '" . (int)$data['sort_order'] . "',
            `date_published` = '" . $this->db->escape($data['date_published']) . "',
            `date_modified` = NOW()
            WHERE `blog_id` = '" . (int)$blog_id . "'");
    }

    public function deleteBlog($blog_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "egeser_blog_post` WHERE `blog_id` = '" . (int)$blog_id . "'");
    }

    public function getBlog($blog_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "egeser_blog_post` WHERE `blog_id` = '" . (int)$blog_id . "'");

        return $query->row;
    }

    public function getBlogBySlug($slug, $exclude_id = 0) {
        $query = $this->db->query("SELECT `blog_id` FROM `" . DB_PREFIX . "egeser_blog_post`
            WHERE `slug` = '" . $this->db->escape($slug) . "'
            AND `blog_id` != '" . (int)$exclude_id . "'
            LIMIT 1");

        return $query->row;
    }

    public function getBlogs($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "egeser_blog_post`";

        $sort_data = array(
            'title',
            'status',
            'date_published',
            'sort_order'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY date_published";
        }

        if (isset($data['order']) && ($data['order'] == 'ASC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalBlogs() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "egeser_blog_post`");

        return $query->row['total'];
    }
}
