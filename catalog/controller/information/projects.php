<?php
/**
 * Compatibility alias for Egeser project archive.
 * Existing live URL /referanslar is still category path 133.
 * This controller is not forced into routing; it provides a future-safe alias.
 */
class ControllerInformationProjects extends Controller {
    public function index() {
        $this->request->get['path'] = '133';
        return $this->load->controller('product/category');
    }
}
