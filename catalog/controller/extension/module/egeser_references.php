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

                if (!empty($project['image']) && is_file(DIR_IMAGE . $project['image'])) {
                    $image = $this->model_tool_image->resize($project['image'], 760, 570);
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
                    'eyebrow' => isset($project['eyebrow']) ? $project['eyebrow'] : '',
                    'type' => isset($project['type']) ? $project['type'] : '',
                    'title' => isset($project['title']) ? $project['title'] : '',
                    'description' => isset($project['description']) ? $project['description'] : '',
                    'location' => isset($project['location']) ? $project['location'] : '',
                    'size' => isset($project['size']) ? $project['size'] : '',
                    'link' => $link
                );

                if (count($projects) >= 6) {
                    break;
                }
            }
        }

        if (!$projects) {
            return '';
        }

        $data['title'] = !empty($setting['title'])
            ? $setting['title']
            : 'Gerçek uygulamalar güvenin en güçlü kanıtıdır.';

        $data['subtitle'] = !empty($setting['subtitle'])
            ? $setting['subtitle']
            : '';

        $data['projects'] = $projects;

        $this->document->addStyle('catalog/view/theme/egeser/stylesheet/egeser-references.css');

        return $this->load->view('extension/module/egeser_references.tpl', $data);
    }
}
