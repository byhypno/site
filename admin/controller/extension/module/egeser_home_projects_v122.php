<?php
class ControllerExtensionModuleEgeserHomeProjectsV122 extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_home_projects_v122');
        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $save = array(
                'egeser_home_projects_v122_status' => !empty($this->request->post['egeser_home_projects_v122_status']) ? 1 : 0,
                'egeser_home_projects_v122_projects' => isset($this->request->post['egeser_home_projects_v122_projects']) && is_array($this->request->post['egeser_home_projects_v122_projects'])
                    ? $this->sanitizeProjects($this->request->post['egeser_home_projects_v122_projects'])
                    : array()
            );

            $this->model_setting_setting->editSetting('egeser_home_projects_v122', $save);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link(
                'extension/module/egeser_home_projects_v122',
                'token=' . $this->session->data['token'],
                true
            ));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_help'] = $this->language->get('text_help');

        $data['entry_status'] = $this->language->get('entry_status');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
        $data['success'] = '';

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/egeser_home_projects_v122', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/module/egeser_home_projects_v122', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
        $data['token'] = $this->session->data['token'];

        if (isset($this->request->post['egeser_home_projects_v122_status'])) {
            $data['status'] = (int)$this->request->post['egeser_home_projects_v122_status'];
        } else {
            $data['status'] = (int)$this->config->get('egeser_home_projects_v122_status');
        }

        if (isset($this->request->post['egeser_home_projects_v122_projects'])) {
            $data['projects'] = $this->request->post['egeser_home_projects_v122_projects'];
        } else {
            $saved = $this->config->get('egeser_home_projects_v122_projects');
            $data['projects'] = is_array($saved) ? $saved : array();
        }

        $defaults = array(
            array(
                'enabled' => 1,
                'image' => '',
                'eyebrow' => 'BİREYSEL',
                'type' => 'Prefabrik Ev',
                'title' => '85 m² Prefabrik Ev Uygulaması',
                'description' => 'Gerçek teslim projesi için kısa proje özeti.',
                'location' => 'İzmir / Manisa',
                'size' => '85 m²',
                'link' => ''
            ),
            array(
                'enabled' => 1,
                'image' => '',
                'eyebrow' => 'KURUMSAL',
                'type' => 'Ofis & Yönetim',
                'title' => 'Prefabrik Ofis / Yönetim Projesi',
                'description' => 'Kurumsal kullanım için tamamlanan prefabrik yapı uygulaması.',
                'location' => 'İzmir',
                'size' => '',
                'link' => ''
            ),
            array(
                'enabled' => 1,
                'image' => '',
                'eyebrow' => 'KURUMSAL',
                'type' => 'Yatakhane / Sosyal Tesis',
                'title' => 'Personel ve Sosyal Tesis Projesi',
                'description' => 'Toplu kullanım ihtiyacına yönelik prefabrik yapı referansı.',
                'location' => 'Manisa',
                'size' => '',
                'link' => ''
            )
        );

        for ($i = 0; $i < 3; $i++) {
            if (!isset($data['projects'][$i]) || !is_array($data['projects'][$i])) {
                $data['projects'][$i] = $defaults[$i];
            } else {
                $data['projects'][$i] = array_merge($defaults[$i], $data['projects'][$i]);
            }
        }

        $data['placeholder'] = 'view/image/placeholder.png';

        $this->load->model('tool/image');

        foreach ($data['projects'] as $key => $project) {
            if (!empty($project['image']) && is_file(DIR_IMAGE . $project['image'])) {
                $data['projects'][$key]['thumb'] = $this->model_tool_image->resize($project['image'], 220, 150);
            } else {
                $data['projects'][$key]['thumb'] = $this->model_tool_image->resize('no_image.png', 220, 150);
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/egeser_home_projects_v122.tpl', $data));
    }

    public function install() {
        $this->load->model('user/user_group');

        $this->model_user_user_group->addPermission(
            $this->user->getGroupId(),
            'access',
            'extension/module/egeser_home_projects_v122'
        );

        $this->model_user_user_group->addPermission(
            $this->user->getGroupId(),
            'modify',
            'extension/module/egeser_home_projects_v122'
        );
    }

    private function sanitizeProjects($projects) {
        $clean = array();

        for ($i = 0; $i < 3; $i++) {
            $row = isset($projects[$i]) && is_array($projects[$i]) ? $projects[$i] : array();

            $clean[$i] = array(
                'enabled' => !empty($row['enabled']) ? 1 : 0,
                'image' => isset($row['image']) ? trim($row['image']) : '',
                'eyebrow' => isset($row['eyebrow']) ? trim(strip_tags($row['eyebrow'])) : '',
                'type' => isset($row['type']) ? trim(strip_tags($row['type'])) : '',
                'title' => isset($row['title']) ? trim(strip_tags($row['title'])) : '',
                'description' => isset($row['description']) ? trim(strip_tags($row['description'])) : '',
                'location' => isset($row['location']) ? trim(strip_tags($row['location'])) : '',
                'size' => isset($row['size']) ? trim(strip_tags($row['size'])) : '',
                'link' => isset($row['link']) ? trim($row['link']) : ''
            );
        }

        return $clean;
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_home_projects_v122')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['egeser_home_projects_v122_projects']) && is_array($this->request->post['egeser_home_projects_v122_projects'])) {
            foreach ($this->request->post['egeser_home_projects_v122_projects'] as $project) {
                if (!empty($project['enabled']) && empty(trim($project['title']))) {
                    $this->error['warning'] = $this->language->get('error_title');
                    break;
                }
            }
        }

        return !$this->error;
    }
}
