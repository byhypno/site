<?php
class ModelExtensionModuleEgeserSkeleton extends Model {
    private function languageId() {
        $id = (int)$this->config->get('config_language_id');
        return $id > 0 ? $id : 1;
    }

    private function findAlias($slug) {
        $q = $this->db->query("SELECT `query` FROM `" . DB_PREFIX . "url_alias` WHERE `keyword` = '" . $this->db->escape($slug) . "' LIMIT 1");
        return $q->num_rows ? (string)$q->row['query'] : '';
    }

    private function defs() {
        return array(
            'categories'=>array(
                'prefabrik_yapilar'=>array('name'=>'Prefabrik Yapılar','slug'=>'prefabrik-yapilar','parent'=>'','sort'=>10,'meta'=>'Prefabrik Yapılar | Egeser Prefabrik','desc'=>'Bireysel ve kurumsal prefabrik yapı çözümleri.'),
                'tek_katli'=>array('name'=>'Tek Katlı Prefabrik Evler','slug'=>'tek-katli-prefabrik-evler','parent'=>'prefabrik_yapilar','sort'=>10,'meta'=>'Tek Katlı Prefabrik Evler | Egeser Prefabrik','desc'=>'Tek katlı prefabrik ev modelleri ve proje çözümleri.'),
                'cift_katli'=>array('name'=>'Çift Katlı Prefabrik Evler','slug'=>'cift-katli-prefabrik-evler','parent'=>'prefabrik_yapilar','sort'=>20,'meta'=>'Çift Katlı Prefabrik Evler | Egeser Prefabrik','desc'=>'Çift katlı ve dubleks prefabrik ev çözümleri.'),
                'ofis_yonetim'=>array('name'=>'Prefabrik Ofis ve Yönetim Binaları','slug'=>'prefabrik-ofis-ve-yonetim-binalari','parent'=>'prefabrik_yapilar','sort'=>30,'meta'=>'Prefabrik Ofis ve Yönetim Binaları | Egeser Prefabrik','desc'=>'Kurumsal ofis ve yönetim binası prefabrik çözümleri.'),
                'yatakhane'=>array('name'=>'Prefabrik Yatakhane Binaları','slug'=>'prefabrik-yatakhane-binalari','parent'=>'prefabrik_yapilar','sort'=>40,'meta'=>'Prefabrik Yatakhane Binaları | Egeser Prefabrik','desc'=>'Personel konaklama ve yatakhane prefabrik yapı çözümleri.'),
                'yemekhane'=>array('name'=>'Prefabrik Yemekhane Binaları','slug'=>'prefabrik-yemekhane-binalari','parent'=>'prefabrik_yapilar','sort'=>50,'meta'=>'Prefabrik Yemekhane Binaları | Egeser Prefabrik','desc'=>'Toplu kullanım için prefabrik yemekhane çözümleri.'),
                'santiye'=>array('name'=>'Prefabrik Şantiye Yapıları','slug'=>'prefabrik-santiye-yapilari','parent'=>'prefabrik_yapilar','sort'=>60,'meta'=>'Prefabrik Şantiye Yapıları | Egeser Prefabrik','desc'=>'Şantiye ve saha kullanımına uygun prefabrik yapılar.'),
                'sosyal_tesis'=>array('name'=>'Prefabrik Sosyal Tesis Yapıları','slug'=>'prefabrik-sosyal-tesis-yapilari','parent'=>'prefabrik_yapilar','sort'=>70,'meta'=>'Prefabrik Sosyal Tesis Yapıları | Egeser Prefabrik','desc'=>'Sosyal tesis ve ortak kullanım alanları için prefabrik çözümler.'),
                'ozel_proje'=>array('name'=>'Özel Proje Prefabrik Yapılar','slug'=>'ozel-proje-prefabrik-yapilar','parent'=>'prefabrik_yapilar','sort'=>80,'meta'=>'Özel Proje Prefabrik Yapılar | Egeser Prefabrik','desc'=>'İhtiyaca özel projelendirilen prefabrik yapı çözümleri.')
            ),
            'information'=>array(
                'hakkimizda'=>array('title'=>'Hakkımızda','slug'=>'hakkimizda','sort'=>10,'meta'=>'Hakkımızda | Egeser Prefabrik','desc'=>'Egeser Prefabrik hakkında kurumsal bilgiler.'),
                'kurumsal'=>array('title'=>'Kurumsal','slug'=>'kurumsal','sort'=>20,'meta'=>'Kurumsal | Egeser Prefabrik','desc'=>'Egeser Prefabrik kurumsal yapı, üretim ve hizmet yaklaşımı.'),
                'referanslar'=>array('title'=>'Projelerimiz ve Referanslar','slug'=>'referanslar','sort'=>30,'meta'=>'Prefabrik Projeler ve Referanslar | Egeser Prefabrik','desc'=>'Tamamlanan bireysel ve kurumsal prefabrik projeler.'),
                'teknik'=>array('title'=>'Teknik Bilgiler','slug'=>'teknik-bilgiler','sort'=>40,'meta'=>'Prefabrik Yapı Teknik Bilgileri | Egeser Prefabrik','desc'=>'Prefabrik yapı sistemi, duvar, çatı, yalıtım ve montaj hakkında teknik bilgiler.'),
                'e_katalog'=>array('title'=>'E-Katalog','slug'=>'e-katalog','sort'=>50,'meta'=>'E-Katalog | Egeser Prefabrik','desc'=>'Egeser Prefabrik ürün ve proje kataloğu.')
            )
        );
    }

    public function status() {
        $rows=array();
        foreach ($this->defs() as $type=>$defs) {
            foreach ($defs as $key=>$d) {
                $query=$this->findAlias($d['slug']);
                $rows[]=array('type'=>$type==='categories'?'Kategori':'Sayfa','name'=>isset($d['name'])?$d['name']:$d['title'],'slug'=>$d['slug'],'exists'=>$query!=='' ,'query'=>$query);
            }
        }
        return $rows;
    }

    public function apply() {
        $this->load->model('catalog/category');
        $this->load->model('catalog/information');
        $lang=$this->languageId();
        $result=array();
        $ids=array();
        $defs=$this->defs();

        foreach ($defs['categories'] as $key=>$d) {
            $existing=$this->findAlias($d['slug']);
            if ($existing && preg_match('/^category_id=(\\d+)$/',$existing,$m)) {
                $ids[$key]=(int)$m[1];
                $result[]=array('status'=>'success','message'=>'Mevcut kategori korundu: '.$d['name'].' (#'.$ids[$key].')');
                continue;
            }
            if ($existing) {
                $result[]=array('status'=>'warning','message'=>'SEO URL başka bir kayıt tarafından kullanılıyor, atlandı: '.$d['slug'].' → '.$existing);
                continue;
            }
            $parent=(!empty($d['parent']) && isset($ids[$d['parent']])) ? (int)$ids[$d['parent']] : 0;
            $data=array(
                'parent_id'=>$parent,
                'top'=>$parent===0 ? 1 : 0,
                'column'=>1,
                'sort_order'=>(int)$d['sort'],
                'status'=>1,
                'category_description'=>array($lang=>array('name'=>$d['name'],'description'=>'<p>'.$d['desc'].'</p>','meta_title'=>$d['meta'],'meta_description'=>$d['desc'],'meta_keyword'=>'')),
                'category_store'=>array(0),
                'category_layout'=>array(),
                'keyword'=>$d['slug']
            );
            $id=$this->model_catalog_category->addCategory($data);
            $ids[$key]=(int)$id;
            $result[]=array('status'=>'success','message'=>'Kategori oluşturuldu: '.$d['name'].' (#'.$id.')');
        }

        foreach ($defs['information'] as $key=>$d) {
            $existing=$this->findAlias($d['slug']);
            if ($existing && preg_match('/^information_id=(\\d+)$/',$existing,$m)) {
                $result[]=array('status'=>'success','message'=>'Mevcut sayfa korundu: '.$d['title'].' (#'.(int)$m[1].')');
                continue;
            }
            if ($existing) {
                $result[]=array('status'=>'warning','message'=>'SEO URL başka bir kayıt tarafından kullanılıyor, atlandı: '.$d['slug'].' → '.$existing);
                continue;
            }
            $data=array(
                'bottom'=>0,
                'sort_order'=>(int)$d['sort'],
                'status'=>1,
                'information_description'=>array($lang=>array('title'=>$d['title'],'description'=>'<p>'.$d['desc'].'</p>','meta_title'=>$d['meta'],'meta_description'=>$d['desc'],'meta_keyword'=>'')),
                'information_store'=>array(0),
                'information_layout'=>array(),
                'keyword'=>$d['slug']
            );
            $id=$this->model_catalog_information->addInformation($data);
            $result[]=array('status'=>'success','message'=>'Sayfa oluşturuldu: '.$d['title'].' (#'.$id.')');
        }
        return $result;
    }
}
