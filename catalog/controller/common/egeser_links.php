<?php
class ControllerCommonEgeserLinks extends Controller {
    public function index() {
        $this->load->library('egeser_url_resolver');
        $r = new EgeserUrlResolver($this->registry);

        $map = array(
            'prefabrik_yapilar' => 'prefabrik-yapilar',
            'tek_katli' => 'tek-katli-prefabrik-evler',
            'cift_katli' => 'cift-katli-prefabrik-evler',
            'ofis' => 'prefabrik-ofis-ve-yonetim-binalari',
            'yatakhane' => 'prefabrik-yatakhane-binalari',
            'yemekhane' => 'prefabrik-yemekhane-binalari',
            'santiye' => 'prefabrik-santiye-yapilari',
            'sosyal_tesis' => 'prefabrik-sosyal-tesis-yapilari',
            'ozel_proje' => 'ozel-proje-prefabrik-yapilar',
            'hakkimizda' => 'hakkimizda',
            'kurumsal' => 'kurumsal',
            'referanslar' => 'referanslar',
            'teknik_bilgiler' => 'teknik-bilgiler',
            'e_katalog' => 'e-katalog'
        );

        $data = array();
        foreach ($map as $key => $keyword) {
            $data[$key] = $r->routeByKeyword($keyword, '#');
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($data));
    }
}
