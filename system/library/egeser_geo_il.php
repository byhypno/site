<?php
/**
 * Egeser Ziyaretci & Lead Takip Merkezi - il (sehir) referans listesi ve
 * IP -> il tespiti. Plaka kodlari (1-81) harita SVG'sindeki
 * data-city-code degerleriyle birebir aynidir, boylece ziyaretci verisi
 * ile harita arasinda isim eslestirmesi gerekmez.
 *
 * IP -> il verisi oc_egeser_geo_ip_range tablosundan gelir (DB-IP City
 * Lite, CC BY 4.0, sadece Turkiye IPv4 araliklari - bkz. teslim edilen
 * 09_GEOIP_IL_TESPITI_KUR.sql). Tablo bos/eksikse tespit sessizce atlanir.
 */
class EgeserGeoIl {
    const NAME_BY_CODE = array(
        1 => 'Adana', 2 => 'Adıyaman', 3 => 'Afyonkarahisar', 4 => 'Ağrı', 5 => 'Amasya',
        6 => 'Ankara', 7 => 'Antalya', 8 => 'Artvin', 9 => 'Aydın', 10 => 'Balıkesir',
        11 => 'Bilecik', 12 => 'Bingöl', 13 => 'Bitlis', 14 => 'Bolu', 15 => 'Burdur',
        16 => 'Bursa', 17 => 'Çanakkale', 18 => 'Çankırı', 19 => 'Çorum', 20 => 'Denizli',
        21 => 'Diyarbakır', 22 => 'Edirne', 23 => 'Elazığ', 24 => 'Erzincan', 25 => 'Erzurum',
        26 => 'Eskişehir', 27 => 'Gaziantep', 28 => 'Giresun', 29 => 'Gümüşhane', 30 => 'Hakkâri',
        31 => 'Hatay', 32 => 'Isparta', 33 => 'Mersin', 34 => 'İstanbul', 35 => 'İzmir',
        36 => 'Kars', 37 => 'Kastamonu', 38 => 'Kayseri', 39 => 'Kırklareli', 40 => 'Kırşehir',
        41 => 'Kocaeli', 42 => 'Konya', 43 => 'Kütahya', 44 => 'Malatya', 45 => 'Manisa',
        46 => 'Kahramanmaraş', 47 => 'Mardin', 48 => 'Muğla', 49 => 'Muş', 50 => 'Nevşehir',
        51 => 'Niğde', 52 => 'Ordu', 53 => 'Rize', 54 => 'Sakarya', 55 => 'Samsun',
        56 => 'Siirt', 57 => 'Sinop', 58 => 'Sivas', 59 => 'Tekirdağ', 60 => 'Tokat',
        61 => 'Trabzon', 62 => 'Tunceli', 63 => 'Şanlıurfa', 64 => 'Uşak', 65 => 'Van',
        66 => 'Yozgat', 67 => 'Zonguldak', 68 => 'Aksaray', 69 => 'Bayburt', 70 => 'Karaman',
        71 => 'Kırıkkale', 72 => 'Batman', 73 => 'Şırnak', 74 => 'Bartın', 75 => 'Ardahan',
        76 => 'Iğdır', 77 => 'Yalova', 78 => 'Karabük', 79 => 'Kilis', 80 => 'Osmaniye',
        81 => 'Düzce'
    );

    // Egeser'in hizmet bolgesi (llms.txt / site icerigiyle birebir ayni).
    const EGE_SERVICE_CODES = array(35, 45, 9, 64, 10, 48); // İzmir, Manisa, Aydın, Uşak, Balıkesir, Muğla

    public static function slug($name) {
        $map = array(
            'ç'=>'c','ğ'=>'g','ı'=>'i','ö'=>'o','ş'=>'s','ü'=>'u','â'=>'a','î'=>'i','û'=>'u',
            'Ç'=>'c','Ğ'=>'g','I'=>'i','İ'=>'i','Ö'=>'o','Ş'=>'s','Ü'=>'u','Â'=>'a','Î'=>'i','Û'=>'u'
        );
        return strtolower(strtr(trim($name), $map));
    }

    public static function slugByCode($code) {
        return isset(self::NAME_BY_CODE[$code]) ? self::slug(self::NAME_BY_CODE[$code]) : '';
    }

    public static function codeByName($name) {
        $needle = self::slug($name);
        foreach (self::NAME_BY_CODE as $code => $official) {
            if (self::slug($official) === $needle) {
                return $code;
            }
        }
        return 0;
    }

    /**
     * Verilen IPv4 adresinin ilini tespit eder. IPv6, gecersiz IP veya
     * eslesme bulunamazsa 0 doner. Ham IP hicbir yerde saklanmaz; bu
     * fonksiyon sadece tek seferlik sorgu icin kullanir, sonucu disinda
     * hicbir iz birakmaz.
     */
    public static function resolveIlCode($db, $ip) {
        $long = ip2long((string)$ip);
        if ($long === false) {
            return 0; // IPv6 veya gecersiz adres - v1 kapsami disinda
        }
        $unsigned = sprintf('%u', $long);

        try {
            $q = $db->query("SELECT ip_to, il_code FROM `" . DB_PREFIX . "egeser_geo_ip_range`
                WHERE ip_from <= " . (int)$unsigned . "
                ORDER BY ip_from DESC LIMIT 1");
        } catch (Exception $e) {
            return 0; // tablo henuz import edilmemis olabilir
        }

        if (!$q->num_rows) {
            return 0;
        }

        if (sprintf('%u', $q->row['ip_to']) >= $unsigned) {
            return (int)$q->row['il_code'];
        }

        return 0;
    }

    /**
     * Koyuluk haritasi icin renk uretir. count=0 icin notr acik gri,
     * count>0 icin maksimuma gore 5 kademeli mavi tonu.
     */
    public static function colorScale($count, $max) {
        if ($count <= 0) {
            return '#eef2f6';
        }
        if ($max <= 0) {
            $max = 1;
        }

        $ratio = min(1, $count / $max);
        $steps = array('#c7dcf0', '#8fb8df', '#5790c8', '#2f6aa8', '#163f66');
        $index = (int)ceil($ratio * count($steps)) - 1;
        $index = max(0, min(count($steps) - 1, $index));

        return $steps[$index];
    }

    /**
     * $il_counts: il_code => ziyaretci sayisi. Haritadaki her il grubu
     * icin id selektorlu bir fill kurali uretir (0 olanlar varsayilan
     * notr rengi CSS'ten alir, override gerekmez).
     */
    public static function buildStyleCss(array $il_counts) {
        $max = $il_counts ? max($il_counts) : 0;
        $css = '';

        foreach ($il_counts as $code => $count) {
            if ($count <= 0 || !isset(self::NAME_BY_CODE[$code])) continue;
            $slug = self::slugByCode($code);
            $color = self::colorScale($count, $max);
            $css .= '#' . $slug . ' path{fill:' . $color . ';}';
        }

        return $css;
    }

    /**
     * JS tooltip'i icin: il_code => array(name, count). data-city-code
     * attribute'u ile JS tarafinda eslestirilir.
     */
    public static function buildCountsJson(array $il_counts) {
        $out = array();
        foreach (self::NAME_BY_CODE as $code => $name) {
            $out[$code] = array('name' => $name, 'count' => isset($il_counts[$code]) ? (int)$il_counts[$code] : 0);
        }

        return json_encode($out);
    }
}
