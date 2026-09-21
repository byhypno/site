<?php
class ControllerExtensionModuleEgeserReferences extends Controller {
    public function index($setting) {
        if (empty($setting['status'])) {
            return '';
        }

        $projects = array();

        if (!empty($setting['projects']) && is_array($setting['projects'])) {
            $this->load->model('tool/image');

            foreach ($setting['projects'] as $project) {
                if (empty($project['enabled']) || empty($project['title'])) {
                    continue;
                }

                $image = '';
                $image_large = '';

                if (!empty($project['image']) && is_file(DIR_IMAGE . $project['image'])) {
                    $image = $this->model_tool_image->resize($project['image'], 640, 640);
                    $image_large = $this->model_tool_image->resize($project['image'], 1400, 1400);
                }

                $link = isset($project['link']) ? trim($project['link']) : '';

                if ($link !== '') {
                    $is_relative = strpos($link, '/') === 0;
                    $is_route = strpos($link, 'index.php?route=') === 0;
                    $is_http = preg_match('#^https?://#i', $link);

                    if (!$is_relative && !$is_route && !$is_http) {
                        $link = '';
                    }
                }

                $projects[] = array(
                    'image' => $image,
                    'image_large' => $image_large,
                    'eyebrow' => isset($project['eyebrow']) ? $project['eyebrow'] : '',
                    'type' => isset($project['type']) ? $project['type'] : '',
                    'title' => isset($project['title']) ? $project['title'] : '',
                    'description' => isset($project['description']) ? $project['description'] : '',
                    'location' => isset($project['location']) ? $project['location'] : '',
                    'size' => isset($project['size']) ? $project['size'] : '',
                    'link' => $link
                );

                if (count($projects) >= 60) {
                    break;
                }
            }
        }

        $partners = array();

        if (!empty($setting['partners']) && is_array($setting['partners'])) {
            $this->load->model('tool/image');

            foreach ($setting['partners'] as $partner) {
                if (empty($partner['enabled']) || empty($partner['image']) || !is_file(DIR_IMAGE . $partner['image'])) {
                    continue;
                }

                $link = isset($partner['link']) ? trim($partner['link']) : '';

                if ($link !== '' && !preg_match('#^https?://#i', $link) && strpos($link, '/') !== 0) {
                    $link = '';
                }

                $partners[] = array(
                    'image' => $this->model_tool_image->resize($partner['image'], 180, 90),
                    'name' => isset($partner['name']) ? $partner['name'] : '',
                    'description' => isset($partner['description']) ? $partner['description'] : '',
                    'link' => $link
                );

                if (count($partners) >= 8) {
                    break;
                }
            }
        }

        if (!$projects && !$partners) {
            return '';
        }

        $data['title'] = !empty($setting['title'])
            ? $setting['title']
            : 'Gerçek uygulamalar güvenin en güçlü kanıtıdır.';

        $data['subtitle'] = !empty($setting['subtitle'])
            ? $setting['subtitle']
            : '';

        $data['projects'] = $projects;
        $data['partners'] = $partners;

        $reference_css_path = DIR_TEMPLATE . 'egeser/stylesheet/egeser-references.css';
        $reference_css_version = is_file($reference_css_path) ? filemtime($reference_css_path) : time();
        $this->document->addStyle('catalog/view/theme/egeser/stylesheet/egeser-references.css?v=' . $reference_css_version);

        return $this->load->view('extension/module/egeser_references.tpl', $data);
    }
}
