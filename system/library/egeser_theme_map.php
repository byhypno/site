<?php
/**
 * Egeser Theme V8.1 URL / Category Map
 * Mevcut SEO URL'leri korunacak şekilde hazırlanmıştır.
 *
 * NOT:
 * Bu dosya yönlendirme yapmaz.
 * Mevcut URL değerlerini tema içinde merkezi olarak kullanır.
 */
class EgeserThemeMap {
    public static function categories() {
        return array(
            'prefabrik_yapilar' => array(
                'category_id' => 91,
                'url' => 'prefabrik-yapilar'
            ),
            'tek_katli' => array(
                'category_id' => 127,
                'url' => 'tek-katli-prefabrik-evler'
            ),
            'cift_katli' => array(
                'category_id' => 128,
                'url' => 'cift-katli-prefabrik-evler'
            ),
            'ofis_yonetim' => array(
                'category_id' => 103,
                'url' => 'prefabrik-ofis-ve-yonetim-binalari'
            ),
            'yatakhane' => array(
                'category_id' => 104,
                'url' => 'prefabrik-yatakhane-binalari'
            ),
            'yemekhane' => array(
                'category_id' => 105,
                'url' => 'prefabrik-yemekhane-binalari'
            ),
            'referanslar' => array(
                'category_id' => 133,
                'url' => 'referanslar'
            ),
            'e_katalog' => array(
                'category_id' => 136,
                'url' => 'e-katalog'
            ),
            'videolar' => array(
                'category_id' => 139,
                'url' => 'videolar'
            ),
            'iletisim' => array(
                'category_id' => 97,
                'url' => 'iletisim'
            ),
            'kurumsal' => array(
                'category_id' => 98,
                'url' => 'kurumsal'
            ),
            'hakkimizda' => array(
                'category_id' => 99,
                'url' => 'hakkimizda'
            )
        );
    }

    public static function products() {
        return array(
            101 => '32-m2-tek-katli-prefabrik-ev',
            107 => '40-m2-tek-katli-prefabrik-ev-verandali',
            108 => '40-m2-tek-katli-jumbo-cati-prefabrik-ev',
            109 => '50-m2-tek-katli-prefabrik-ev',
            110 => '52-m2-tek-katli-prefabrik-ev',
            113 => '78-m2-tek-katli-prefabrik-ev',
            156 => '80-m-tek-katli-prefabrik-ev',
            244 => '123-m-tek-katli-prefabrik-ev',
            245 => '138-m2-tek-katli-prefabrik-ev',
            247 => '142-m2-prefabrik-ev',
            248 => '80-m2-dubleks-prefabrik-ev',
            249 => '92-m2-dubleks-prefabrik-ev',
            250 => '122-m2-dubleks-prefabrik-ev',
            251 => '131-m2-dubleks-prefabrik-ev',
            252 => '149-m2-dubleks-prefabrik-ev',
            253 => '155-m2-dubleks-prefabrik-ev',
            61  => '60-m2-tek-katli-prefabrik-ev-genis-verandali',
            65  => '96-m2-tek-katli-prefabrik-ev',
            76  => '60-m2-tek-katli-prefabrik-ev',
            81  => '59-m2-tek-katli-prefabrik-ev',
            82  => '64-m2-tek-katli-prefabrik-ev',
            85  => '69-m2-tek-katli-prefabrik-ev',
            86  => '74-m2-tek-katli-prefabrik-ev',
            90  => '101-m2-tek-katli-prefabrik-ev',
            93  => '109-m-tek-katli-prefabrik-ev',
            97  => '105-m2-tek-katli-prefabrik-ev',
            98  => '46-m2-tek-katli-prefabrik-ev'
        );
    }

    public static function infoPages() {
        return array(
            'iade_degisim' => array('information_id'=>7, 'url'=>'iade-ve-degisim'),
            'gonderi_nakliye' => array('information_id'=>8, 'url'=>'gonderi-ve-nakliye')
        );
    }

    public static function manufacturer() {
        return array(
            'manufacturer_id' => 11,
            'url' => 'egeser-konteyner-prefabrik'
        );
    }

    public static function absolute($base, $slug) {
        return rtrim($base, '/') . '/' . ltrim($slug, '/');
    }
}
