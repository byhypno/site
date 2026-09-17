<?php
class ControllerExtensionModuleEgeserVisualManager extends Controller {
    public function index() {
        if (!$this->config->get('egeser_visual_manager_status')) return '';

        $preview_only = (int)$this->config->get('egeser_visual_manager_preview_only');
        if ($preview_only && (!isset($this->request->get['egeser_visual_preview']) || $this->request->get['egeser_visual_preview'] != '1')) {
            return '';
        }

        $rules = $this->config->get('egeser_visual_manager_rules');
        if (!is_array($rules) || !$rules) return '';

        $clean = array();
        foreach ($rules as $rule) {
            if (empty($rule['status']) || empty($rule['image']) || empty($rule['target'])) continue;
            $clean[] = array(
                'label' => isset($rule['label']) ? $rule['label'] : '',
                'match_type' => isset($rule['match_type']) ? $rule['match_type'] : 'text',
                'target' => $rule['target'],
                'apply_type' => isset($rule['apply_type']) ? $rule['apply_type'] : 'img',
                'image' => $rule['image'],
                'alt' => isset($rule['alt']) ? $rule['alt'] : '',
                'fit' => isset($rule['fit']) ? $rule['fit'] : 'cover',
                'position' => isset($rule['position']) ? $rule['position'] : '50% 50%'
            );
        }
        if (!$clean) return '';

        $data['rules_json'] = json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $data['version'] = (int)$this->config->get('egeser_visual_manager_version');
        return $this->load->view('extension/module/egeser_visual_manager', $data);
    }
}
