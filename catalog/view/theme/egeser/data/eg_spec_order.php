<?php
/* EGESER - Ürün detay "Teknik Özellikler" sıralaması.
   Bu dosya admin > Katalog > Teknik Özellik Sırası ekranından
   sürükle-bırak ile güncellenir. Elle düzenlenebilir ama normalde
   buna gerek yok. */
return array(
    'buckets' => array(
        'Plan ve Model',
        'Yapı ve Yalıtım',
        'İç Mekân',
        'Elektrik ve Tesisat'
    ),
    'rows' => array(
        'Plan ve Model' => array(
            'Alan',
            'Oda Sayısı',
            'Kat Sayısı',
            'Yapı Tipi'
        ),
        'Yapı ve Yalıtım' => array(
            'Duvar Kalınlığı',
            'Çatı Sistemi'
        ),
        'İç Mekân' => array(
            'Tavan',
            'Zemin',
            'PVC Doğrama',
            'Cam Sistemi',
            'İç Kapılar',
            'Dış Kapı',
            'Mutfak',
            'Banyo'
        ),
        'Elektrik ve Tesisat' => array(
            'Elektrik Tesisatı',
            'Sıhhi Tesisat'
        )
    )
);
