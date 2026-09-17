<?php
class ControllerInformationEgeserBlog extends Controller {
    public function index() {
        $this->load->model('catalog/egeser_blog');
        $this->load->model('tool/image');

        if (isset($this->request->get['blog_id']) && (int)$this->request->get['blog_id'] > 0) {
            return $this->post((int)$this->request->get['blog_id']);
        }

        $base = rtrim($this->config->get('config_url'), '/');
        $canonical = $base . '/blog';

        $this->document->setTitle('Prefabrik Ev Rehberi ve Blog | Egeser Prefabrik');
        $this->document->setDescription('Prefabrik evler, yapı malzemeleri, deprem güvenliği, kullanım alanları ve yapı seçimi hakkında Egeser Prefabrik rehber yazıları.');
        $this->document->addLink($canonical, 'canonical');

        $data['breadcrumbs'] = array(
            array('text' => 'Ana Sayfa', 'href' => $base . '/'),
            array('text' => 'Blog', 'href' => $canonical)
        );

        $data['heading_title'] = 'Prefabrik Ev Rehberi ve Blog';
        $data['canonical'] = $canonical;
        $data['posts'] = array();

        foreach ($this->model_catalog_egeser_blog->getPosts() as $post) {
            $image = '';

            if (!empty($post['image']) && defined('DIR_IMAGE') && is_file(DIR_IMAGE . ltrim($post['image'], '/'))) {
                $image = $this->model_tool_image->resize(ltrim($post['image'], '/'), 640, 420);
            }

            $plain = trim(preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode($post['description'], ENT_QUOTES, 'UTF-8'))));

            $data['posts'][] = array(
                'title' => $post['title'],
                'href' => $base . '/blog/' . $post['slug'],
                'image' => $image,
                'excerpt' => utf8_substr($plain, 0, 240) . (utf8_strlen($plain) > 240 ? '…' : ''),
                'date' => !empty($post['date_published']) ? date('d.m.Y', strtotime($post['date_published'])) : ''
            );
        }

        $data['schema'] = array(
            '@context' => 'https://schema.org',
            '@type' => 'Blog',
            'name' => 'Egeser Prefabrik Blog',
            'url' => $canonical,
            'description' => 'Prefabrik ev ve prefabrik yapı konularında rehber içerikler.'
        );

        $data['breadcrumb_schema'] = $this->breadcrumbSchema($data['breadcrumbs']);

        $this->common($data);
        $this->response->setOutput($this->load->view('information/egeser_blog_list', $data));
    }

    private function post($blog_id) {
        $post = $this->model_catalog_egeser_blog->getPostById($blog_id);

        if (!$post) {
            return $this->notFound();
        }

        $base = rtrim($this->config->get('config_url'), '/');
        $canonical = $base . '/blog/' . $post['slug'];

        $this->document->setTitle(!empty($post['meta_title']) ? $post['meta_title'] : $post['title']);
        $this->document->setDescription($post['meta_description']);
        $this->document->setKeywords($post['meta_keyword']);
        $this->document->addLink($canonical, 'canonical');

        $data['breadcrumbs'] = array(
            array('text' => 'Ana Sayfa', 'href' => $base . '/'),
            array('text' => 'Blog', 'href' => $base . '/blog'),
            array('text' => $post['title'], 'href' => $canonical)
        );

        $data['heading_title'] = $post['title'];
        $data['description'] = html_entity_decode($post['description'], ENT_QUOTES, 'UTF-8');
        $data['canonical'] = $canonical;
        $data['date'] = !empty($post['date_published']) ? date('d.m.Y', strtotime($post['date_published'])) : '';
        $data['image'] = '';

        if (!empty($post['image']) && defined('DIR_IMAGE') && is_file(DIR_IMAGE . ltrim($post['image'], '/'))) {
            $data['image'] = $this->model_tool_image->resize(ltrim($post['image'], '/'), 1200, 700);
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => strip_tags($post['title']),
            'mainEntityOfPage' => $canonical,
            'url' => $canonical,
            'description' => $post['meta_description'],
            'datePublished' => date('c', strtotime($post['date_published'])),
            'dateModified' => date('c', strtotime($post['date_modified'])),
            'author' => array(
                '@type' => 'Organization',
                'name' => 'Egeser Prefabrik'
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => 'Egeser Prefabrik',
                'url' => $base . '/'
            )
        );

        if ($data['image']) {
            $schema['image'] = array($data['image']);
        }

        $data['schema'] = $schema;
        $data['breadcrumb_schema'] = $this->breadcrumbSchema($data['breadcrumbs']);

        $this->common($data);
        $this->response->setOutput($this->load->view('information/egeser_blog_post', $data));
    }

    private function notFound() {
        $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
        $this->document->setTitle('Sayfa Bulunamadı');

        $data['breadcrumbs'] = array(
            array('text' => 'Ana Sayfa', 'href' => rtrim($this->config->get('config_url'), '/') . '/'),
            array('text' => 'Blog', 'href' => rtrim($this->config->get('config_url'), '/') . '/blog')
        );
        $data['heading_title'] = 'Sayfa Bulunamadı';
        $data['text_error'] = 'Aradığınız blog yazısı bulunamadı.';
        $data['button_continue'] = 'Ana Sayfaya Dön';
        $data['continue'] = '/';

        $this->common($data);
        $this->response->setOutput($this->load->view('error/not_found', $data));
    }

    private function common(&$data) {
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $data['telephone'] = $this->config->get('config_telephone');
    }

    private function breadcrumbSchema($breadcrumbs) {
        $items = array();
        $position = 1;

        foreach ($breadcrumbs as $item) {
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => trim(strip_tags($item['text'])),
                'item' => html_entity_decode($item['href'], ENT_QUOTES, 'UTF-8')
            );
        }

        return array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        );
    }
}
