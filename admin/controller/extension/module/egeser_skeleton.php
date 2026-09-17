<?php
class ControllerExtensionModuleEgeserSkeleton extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/module/egeser_skeleton');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('extension/module/egeser_skeleton');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_info'] = $this->language->get('text_info');
        $data['button_apply'] = $this->language->get('button_apply');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['token'] = $this->session->data['token'];
        $data['apply_url'] = html_entity_decode($this->url->link('extension/module/egeser_skeleton/apply', 'token=' . $this->session->data['token'], true), ENT_QUOTES, 'UTF-8');
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);
        $data['status_rows'] = $this->model_extension_module_egeser_skeleton->status();
        $data['result'] = isset($this->session->data['egeser_skeleton_result']) ? $this->session->data['egeser_skeleton_result'] : array();
        unset($this->session->data['egeser_skeleton_result']);

        $data['breadcrumbs'] = array(
            array('text'=>$this->language->get('text_home'),'href'=>$this->url->link('common/dashboard','token='.$this->session->data['token'],true)),
            array('text'=>$this->language->get('text_extension'),'href'=>$this->url->link('extension/extension','token='.$this->session->data['token'].'&type=module',true)),
            array('text'=>$data['heading_title'],'href'=>$this->url->link('extension/module/egeser_skeleton','token='.$this->session->data['token'],true))
        );
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/egeser_skeleton.tpl', $data));
    }

    public function apply() {
        $this->load->language('extension/module/egeser_skeleton');
        if (!$this->user->hasPermission('modify', 'extension/module/egeser_skeleton')) {
            $this->session->data['egeser_skeleton_result'] = array(array('status'=>'danger','message'=>$this->language->get('error_permission')));
            $this->response->redirect($this->url->link('extension/module/egeser_skeleton','token='.$this->session->data['token'],true));
            return;
        }
        if ($this->request->server['REQUEST_METHOD'] !== 'POST' || empty($this->request->post['confirm'])) {
            $this->session->data['egeser_skeleton_result'] = array(array('status'=>'warning','message'=>$this->language->get('error_confirm')));
            $this->response->redirect($this->url->link('extension/module/egeser_skeleton','token='.$this->session->data['token'],true));
            return;
        }
        $this->load->model('extension/module/egeser_skeleton');
        $this->session->data['egeser_skeleton_result'] = $this->model_extension_module_egeser_skeleton->apply();
        $this->response->redirect($this->url->link('extension/module/egeser_skeleton','token='.$this->session->data['token'],true));
    }

    public function install() {
        $this->load->model('user/user_group');
        $gid = $this->user->getGroupId();
        $this->model_user_user_group->addPermission($gid, 'access', 'extension/module/egeser_skeleton');
        $this->model_user_user_group->addPermission($gid, 'modify', 'extension/module/egeser_skeleton');
    }

    public function uninstall() {
        // Güvenlik: oluşturulmuş içerikler otomatik silinmez.
    }
}
