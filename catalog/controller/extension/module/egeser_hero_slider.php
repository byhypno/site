<?php
class ControllerExtensionModuleEgeserHeroSlider extends Controller {
    public function index($setting) {
        if (empty($setting['status']) || empty($setting['slides']) || !is_array($setting['slides'])) return '';
        $this->load->model('tool/image');
        $slides = array();
        foreach ($setting['slides'] as $row) {
            if (empty($row['enabled']) || empty($row['image']) || !is_file(DIR_IMAGE . $row['image'])) continue;
            $link = isset($row['link']) ? trim($row['link']) : '';
            if ($link !== '' && strpos($link, '/') !== 0 && strpos($link, 'index.php?route=') !== 0 && !preg_match('#^https?://#i', $link)) $link = '';
            $slides[] = array(
                'image' => $this->model_tool_image->resize($row['image'], 1080, 1080),
                'alt' => isset($row['alt']) && trim($row['alt']) !== '' ? trim($row['alt']) : 'Egeser Prefabrik proje görseli',
                'link' => $link,
                'sort_order' => isset($row['sort_order']) ? (int)$row['sort_order'] : 0
            );
        }
        if (!$slides) return '';
        usort($slides, function($a, $b) { return $a['sort_order'] == $b['sort_order'] ? 0 : ($a['sort_order'] < $b['sort_order'] ? -1 : 1); });
        $data['slides'] = array_slice($slides, 0, 6);
        $data['interval'] = isset($setting['interval']) ? max(2500, min(15000, (int)$setting['interval'])) : 5000;
        $data['transition'] = isset($setting['transition']) ? max(250, min(2500, (int)$setting['transition'])) : 900;
        $data['pause_hover'] = !empty($setting['pause_hover']) ? 1 : 0;
        $this->document->addStyle('catalog/view/theme/egeser/stylesheet/egeser-hero-slider.css');
        return $this->load->view('extension/module/egeser_hero_slider.tpl', $data);
    }
}
