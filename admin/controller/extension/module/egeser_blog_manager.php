<?php
class ControllerExtensionModuleEgeserBlogManager extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_blog_manager');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/egeser_blog_manager');

        $this->getList();
    }

    public function add() {
        $this->load->language('extension/module/egeser_blog_manager');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/egeser_blog_manager');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->request->post['slug'] = $this->resolveSlug($this->request->post);

            $this->model_extension_module_egeser_blog_manager->addBlog($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/egeser_blog_manager', 'token=' . $this->session->data['token'] . $this->urlSuffix(), true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('extension/module/egeser_blog_manager');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/egeser_blog_manager');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->request->post['slug'] = $this->resolveSlug($this->request->post, (int)$this->request->get['blog_id']);

            $this->model_extension_module_egeser_blog_manager->editBlog($this->request->get['blog_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/egeser_blog_manager', 'token=' . $this->session->data['token'] . $this->urlSuffix(), true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('extension/module/egeser_blog_manager');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('extension/module/egeser_blog_manager');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $blog_id) {
                $this->model_extension_module_egeser_blog_manager->deleteBlog($blog_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/egeser_blog_manager', 'token=' . $this->session->data['token'] . $this->urlSuffix(), true));
        }

        $this->getList();
    }

    protected function urlSuffix() {
        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        return $url;
    }

    protected function getList() {
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'date_published';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC';
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

        $url = $this->urlSuffix();

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/egeser_blog_manager', 'token=' . $this->session->data['token'] . $url, true)
        );

        $data['add'] = $this->url->link('extension/module/egeser_blog_manager/add', 'token=' . $this->session->data['token'] . $url, true);
        $data['delete'] = $this->url->link('extension/module/egeser_blog_manager/delete', 'token=' . $this->session->data['token'] . $url, true);

        $this->load->model('tool/image');

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $blog_total = $this->model_extension_module_egeser_blog_manager->getTotalBlogs();
        $results = $this->model_extension_module_egeser_blog_manager->getBlogs($filter_data);

        $data['blogs'] = array();

        foreach ($results as $result) {
            $data['blogs'][] = array(
                'blog_id'        => $result['blog_id'],
                'title'          => $result['title'],
                'status'         => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'date_published' => $result['date_published'] && $result['date_published'] != '0000-00-00 00:00:00' ? date('d.m.Y', strtotime($result['date_published'])) : '',
                'thumb'          => (!empty($result['image']) && is_file(DIR_IMAGE . $result['image'])) ? $this->model_tool_image->resize($result['image'], 40, 40) : $this->model_tool_image->resize('no_image.png', 40, 40),
                'view'           => rtrim($this->config->get('config_url'), '/') . '/blog/' . $result['slug'],
                'edit'           => $this->url->link('extension/module/egeser_blog_manager/edit', 'token=' . $this->session->data['token'] . '&blog_id=' . $result['blog_id'] . $url, true)
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_image'] = $this->language->get('column_image');
        $data['column_title'] = $this->language->get('column_title');
        $data['column_date_published'] = $this->language->get('column_date_published');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_view'] = $this->language->get('button_view');

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['selected'] = isset($this->request->post['selected']) ? (array)$this->request->post['selected'] : array();

        $sort_url = ($order == 'ASC') ? '&order=DESC' : '&order=ASC';

        if (isset($this->request->get['page'])) {
            $sort_url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_title'] = $this->url->link('extension/module/egeser_blog_manager', 'token=' . $this->session->data['token'] . '&sort=title' . $sort_url, true);
        $data['sort_date_published'] = $this->url->link('extension/module/egeser_blog_manager', 'token=' . $this->session->data['token'] . '&sort=date_published' . $sort_url, true);
        $data['sort_status'] = $this->url->link('extension/module/egeser_blog_manager', 'token=' . $this->session->data['token'] . '&sort=status' . $sort_url, true);

        $pagination_url = '';

        if (isset($this->request->get['sort'])) {
            $pagination_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $pagination_url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $blog_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('extension/module/egeser_blog_manager', 'token=' . $this->session->data['token'] . $pagination_url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($blog_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($blog_total - $this->config->get('config_limit_admin'))) ? $blog_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $blog_total, ceil($blog_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/egeser_blog_manager_list', $data));
    }

    protected function getForm() {
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['blog_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_slug_help'] = $this->language->get('text_slug_help');

        $data['entry_title'] = $this->language->get('entry_title');
        $data['entry_slug'] = $this->language->get('entry_slug');
        $data['entry_description'] = $this->language->get('entry_description');
        $data['entry_image'] = $this->language->get('entry_image');
        $data['entry_meta_title'] = $this->language->get('entry_meta_title');
        $data['entry_meta_description'] = $this->language->get('entry_meta_description');
        $data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
        $data['entry_tags'] = $this->language->get('entry_tags');
        $data['text_tags_help'] = $this->language->get('text_tags_help');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_date_published'] = $this->language->get('entry_date_published');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_clear'] = $this->language->get('button_clear');

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['error_title'] = isset($this->error['title']) ? $this->error['title'] : '';
        $data['error_slug'] = isset($this->error['slug']) ? $this->error['slug'] : '';

        $url = $this->urlSuffix();

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/egeser_blog_manager', 'token=' . $this->session->data['token'] . $url, true)
        );

        if (!isset($this->request->get['blog_id'])) {
            $data['action'] = $this->url->link('extension/module/egeser_blog_manager/add', 'token=' . $this->session->data['token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('extension/module/egeser_blog_manager/edit', 'token=' . $this->session->data['token'] . '&blog_id=' . $this->request->get['blog_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('extension/module/egeser_blog_manager', 'token=' . $this->session->data['token'] . $url, true);

        if (isset($this->request->get['blog_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $blog_info = $this->model_extension_module_egeser_blog_manager->getBlog($this->request->get['blog_id']);
        }

        $data['token'] = $this->session->data['token'];

        $fields = array('title', 'slug', 'description', 'meta_title', 'meta_description', 'meta_keyword', 'tags', 'sort_order');

        foreach ($fields as $field) {
            if (isset($this->request->post[$field])) {
                $data[$field] = $this->request->post[$field];
            } elseif (!empty($blog_info)) {
                $data[$field] = $blog_info[$field];
            } else {
                $data[$field] = ($field == 'sort_order') ? 0 : '';
            }
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($blog_info)) {
            $data['status'] = $blog_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['date_published'])) {
            $data['date_published'] = $this->request->post['date_published'];
        } elseif (!empty($blog_info) && !empty($blog_info['date_published']) && $blog_info['date_published'] != '0000-00-00 00:00:00') {
            $data['date_published'] = date('Y-m-d', strtotime($blog_info['date_published']));
        } else {
            $data['date_published'] = date('Y-m-d');
        }

        $this->load->model('tool/image');

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($blog_info)) {
            $data['image'] = $blog_info['image'];
        } else {
            $data['image'] = '';
        }

        if ($data['image'] && is_file(DIR_IMAGE . $data['image'])) {
            $data['thumb'] = $this->model_tool_image->resize($data['image'], 200, 200);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 200, 200);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 200, 200);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/egeser_blog_manager_form', $data));
    }

    private function slugify($text) {
        $map = array(
            'ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g', 'ı' => 'i', 'I' => 'i',
            'İ' => 'i', 'ö' => 'o', 'Ö' => 'o', 'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u'
        );

        $text = strtr($text, $map);
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9]+/u', '-', $text);

        return trim($text, '-');
    }

    private function resolveSlug($post, $blog_id = 0) {
        $base = !empty($post['slug']) ? $this->slugify($post['slug']) : $this->slugify($post['title']);

        if ($base === '') {
            $base = 'yazi';
        }

        $slug = $base;
        $suffix = 2;

        while ($this->model_extension_module_egeser_blog_manager->getBlogBySlug($slug, $blog_id)) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_blog_manager')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['title']) < 3) || (utf8_strlen($this->request->post['title']) > 255)) {
            $this->error['title'] = $this->language->get('error_title');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_blog_manager')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function install() {
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/egeser_blog_manager');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/egeser_blog_manager');
    }

    public function uninstall() {
        // İçeriği bilinçli olarak silmez; blog yazıları veritabanında kalır.
    }
}
