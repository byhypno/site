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
                $image = $this->model_tool_image->resize(ltrim($post['image'], '/'), 640, 640);
            }

            $plain = $this->plainText($post['description']);

            $data['posts'][] = array(
                'title' => $post['title'],
                'href' => $base . '/blog/' . $post['slug'],
                'image' => $image,
                'excerpt' => utf8_substr($plain, 0, 190) . (utf8_strlen($plain) > 190 ? '…' : ''),
                'date' => !empty($post['date_published']) ? date('d.m.Y', strtotime($post['date_published'])) : '',
                'views' => isset($post['views']) ? (int)$post['views'] : 0
            );
        }

        $data['post_count'] = count($data['posts']);

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
        $data['description'] = $this->sanitizePostContent($post['description'], $post['title']);

        $enhanced = $this->enhanceContent($data['description'], $base);
        $data['description'] = $enhanced['content'];
        $data['toc'] = $enhanced['toc'];

        $data['canonical'] = $canonical;
        $data['date'] = !empty($post['date_published']) ? date('d.m.Y', strtotime($post['date_published'])) : '';
        $data['date_modified'] = '';

        if (!empty($post['date_modified']) && !empty($post['date_published'])) {
            $published_day = date('Y-m-d', strtotime($post['date_published']));
            $modified_day = date('Y-m-d', strtotime($post['date_modified']));

            if ($modified_day !== $published_day) {
                $data['date_modified'] = date('d.m.Y', strtotime($post['date_modified']));
            }
        }

        $data['views'] = (isset($post['views']) ? (int)$post['views'] : 0) + 1;
        $data['image'] = '';

        $this->model_catalog_egeser_blog->incrementViews($blog_id);

        if (!empty($post['image']) && defined('DIR_IMAGE') && is_file(DIR_IMAGE . ltrim($post['image'], '/'))) {
            $data['image'] = $this->model_tool_image->resize(ltrim($post['image'], '/'), 1080, 1080);
            $this->document->setImage($data['image']);
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

        $data['related_posts'] = array();

        $related_tags = isset($post['tags']) ? $post['tags'] : '';

        foreach ($this->model_catalog_egeser_blog->getRelatedPosts($blog_id, $related_tags, 3) as $related) {
            $related_image = '';

            if (!empty($related['image']) && defined('DIR_IMAGE') && is_file(DIR_IMAGE . ltrim($related['image'], '/'))) {
                $related_image = $this->model_tool_image->resize(ltrim($related['image'], '/'), 640, 640);
            }

            $data['related_posts'][] = array(
                'title' => $related['title'],
                'href' => $base . '/blog/' . $related['slug'],
                'image' => $related_image,
                'date' => !empty($related['date_published']) ? date('d.m.Y', strtotime($related['date_published'])) : ''
            );
        }

        $this->common($data);
        $this->response->setOutput($this->load->view('information/egeser_blog_post', $data));
    }

    /**
     * Blog şablonu sayfa başlığını zaten tek H1 olarak basar. Editörden gelen
     * içerikteki aynı başlığı kaldırır, diğer H1'leri H2'ye dönüştürür.
     * Böylece mevcut ve gelecekte eklenen yazılar birden fazla H1 üretmez.
     */
    private function sanitizePostContent($description, $title) {
        $content = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
        // Eski içerik aktarımından kalan görünür \n / \r / \t dizilerini temizle.
        $content = str_replace(array('\\n', '\\r', '\\t'), ' ', $content);
        $title_text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($title), ENT_QUOTES, 'UTF-8')));

        $content = preg_replace_callback('/<h1\b[^>]*>(.*?)<\/h1>/is', function($matches) use ($title_text) {
            $heading_text = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($matches[1]), ENT_QUOTES, 'UTF-8')));

            if ($heading_text === $title_text) {
                return '';
            }

            return '<h2>' . $matches[1] . '</h2>';
        }, $content);

        return $content;
    }

    /**
     * H2 başlıklarına atlama linki için id ekler ve içindekiler listesini
     * üretir; ayrıca metin içinde geçen şehir/sayfa adlarının ilk geçtiği
     * yeri ilgili sayfaya bağlar (başlıklara, linklere ve script/style
     * içine dokunmadan).
     */
    private function enhanceContent($content, $base) {
        $content = trim((string)$content);

        if ($content === '') {
            return array('content' => $content, 'toc' => array());
        }

        $keywords = array(
            'İzmir' => $base . '/izmir-prefabrik-ev',
            'Manisa' => $base . '/manisa-prefabrik-ev',
            'Aydın' => $base . '/aydin-prefabrik-ev',
            'Uşak' => $base . '/usak-prefabrik-ev',
            'Balıkesir' => $base . '/balikesir-prefabrik-ev',
            'Muğla' => $base . '/mugla-prefabrik-ev',
            'Projelerimiz' => $base . '/projelerimiz',
            'prefabrik yapılar' => $base . '/prefabrik-yapilar'
        );

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><div id="egeser-root">' . $content . '</div>', LIBXML_NOERROR | LIBXML_NOWARNING);
        libxml_clear_errors();

        $root = $dom->getElementById('egeser-root');

        if (!$root) {
            return array('content' => $content, 'toc' => array());
        }

        $toc = array();
        $used_ids = array();

        foreach (iterator_to_array($dom->getElementsByTagName('h2')) as $heading) {
            $text = trim($heading->textContent);

            if ($text === '') {
                continue;
            }

            $id = $this->slugifyHeading($text);

            if ($id === '') {
                $id = 'bolum';
            }

            $unique_id = $id;
            $suffix = 2;

            while (in_array($unique_id, $used_ids, true)) {
                $unique_id = $id . '-' . $suffix;
                $suffix++;
            }

            $used_ids[] = $unique_id;
            $heading->setAttribute('id', $unique_id);

            $toc[] = array('id' => $unique_id, 'text' => $text);
        }

        $xpath = new DOMXPath($dom);
        $linked = array();

        foreach ($keywords as $keyword => $url) {
            if (isset($linked[$url])) {
                continue;
            }

            $text_nodes = $xpath->query('//text()[not(ancestor::a) and not(ancestor::h1) and not(ancestor::h2) and not(ancestor::h3) and not(ancestor::h4) and not(ancestor::h5) and not(ancestor::h6) and not(ancestor::script) and not(ancestor::style)]');

            foreach ($text_nodes as $node) {
                $pos = mb_stripos($node->nodeValue, $keyword, 0, 'UTF-8');

                if ($pos === false) {
                    continue;
                }

                $matched = mb_substr($node->nodeValue, $pos, mb_strlen($keyword, 'UTF-8'), 'UTF-8');
                $before = mb_substr($node->nodeValue, 0, $pos, 'UTF-8');
                $after = mb_substr($node->nodeValue, $pos + mb_strlen($keyword, 'UTF-8'), null, 'UTF-8');

                $anchor = $dom->createElement('a');
                $anchor->setAttribute('href', $url);
                $anchor->appendChild($dom->createTextNode($matched));

                $parent = $node->parentNode;
                $parent->insertBefore($dom->createTextNode($before), $node);
                $parent->insertBefore($anchor, $node);
                $parent->insertBefore($dom->createTextNode($after), $node);
                $parent->removeChild($node);

                $linked[$url] = true;
                break;
            }
        }

        $html = '';

        foreach ($root->childNodes as $child) {
            $html .= $dom->saveHTML($child);
        }

        return array('content' => $html, 'toc' => $toc);
    }

    private function slugifyHeading($text) {
        $map = array(
            'ç' => 'c', 'Ç' => 'c', 'ğ' => 'g', 'Ğ' => 'g', 'ı' => 'i', 'I' => 'i',
            'İ' => 'i', 'ö' => 'o', 'Ö' => 'o', 'ş' => 's', 'Ş' => 's', 'ü' => 'u', 'Ü' => 'u'
        );

        $text = strtr($text, $map);
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^a-z0-9]+/u', '-', $text);

        return trim($text, '-');
    }

    private function plainText($html) {
        $html = html_entity_decode((string)$html, ENT_QUOTES, 'UTF-8');
        $html = str_replace(array('\\n', '\\r', '\\t'), ' ', $html);
        $html = preg_replace('/<\/(p|div|h[1-6]|li|tr|blockquote)>/iu', ' ', $html);
        return trim(preg_replace('/\s+/u', ' ', strip_tags($html)));
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
        $base = rtrim($this->config->get('config_url'), '/');
        $data['site_base'] = $base;
        $data['blog_url'] = $base . '/blog';
        $data['contact_url'] = $base . '/iletisim';
        $data['models_url'] = $base . '/prefabrik-yapilar';
        $data['whatsapp_url'] = 'https://wa.me/905318866090?text=' . rawurlencode('Merhaba, prefabrik yapı projem için bilgi ve teklif almak istiyorum.');
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
