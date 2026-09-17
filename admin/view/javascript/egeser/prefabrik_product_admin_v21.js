(function($){
'use strict';
var specs=[
['Alan','Örn: 85 m²'],['Oda Sayısı','Örn: 2+1'],['Kat Sayısı','Örn: Tek Katlı'],
['Yapı Tipi','Örn: Prefabrik Ev'],['Veranda','Örn: Var / Yok / 14 m²'],
['Dış Cephe','Dış cephe sistemini yazın'],['İç Duvar','İç duvar sistemini yazın'],
['Duvar Kalınlığı','Örn: 10 cm'],['Çatı Sistemi','Çatı sistemini yazın'],
['Tavan','Tavan uygulamasını yazın'],['Zemin','Zemin uygulamasını yazın'],
['PVC Doğrama','Örn: Isıcamlı PVC doğrama'],['Cam Sistemi','Örn: Isıcam'],
['İç Kapılar','İç kapı özelliklerini yazın'],['Dış Kapı','Dış kapı özelliklerini yazın'],
['Mutfak','Mutfak kapsamını yazın'],['Banyo','Banyo kapsamını yazın'],
['Elektrik Tesisatı','Elektrik tesisatı kapsamını yazın'],
['Sıhhi Tesisat','Sıhhi tesisat kapsamını yazın'],['Isı Yalıtımı','Isı yalıtımı sistemini yazın']
];

function setTabLabel(id,label){var $a=$('a[href="#'+id+'"]'); if($a.length)$a.text(label);}
function hideTab(id){var $a=$('a[href="#'+id+'"]'); if($a.length)$a.closest('li').addClass('egeser-advanced-only');}
function hideField(name){var $e=$('[name="'+name+'"]').first(); if($e.length)$e.closest('.form-group').addClass('egeser-advanced-only');}

function addTopPanel(){
  var $body=$('#form-product .panel-body').first();
  if(!$body.length||$('#egeser-prefabrik-admin').length)return;
  var html='<div id="egeser-prefabrik-admin" class="egeser-v21-panel">'
  +'<div class="egeser-v21-panel__head"><div><div class="egeser-v21-eyebrow">EGESER PREFABRİK ÜRÜN YÖNETİMİ V2.1</div>'
  +'<h3>Hızlı Ürün Girişi</h3><p>Bu sürüm yalnızca admin ürün formunu düzenler; ön yüz ve veritabanına müdahale etmez.</p></div>'
  +'<button type="button" id="egeser-toggle-advanced" class="btn btn-default"><i class="fa fa-sliders"></i> Gelişmiş Modu Aç</button></div>'
  +'<div class="egeser-v21-shortcuts">'
  +'<button type="button" data-tab="tab-general"><b>1</b> Temel Bilgiler</button>'
  +'<button type="button" data-tab="tab-data"><b>2</b> Ürün / SEO</button>'
  +'<button type="button" data-tab="tab-links"><b>3</b> Kategori</button>'
  +'<button type="button" data-tab="tab-attribute"><b>4</b> Teknik Özellikler</button>'
  +'<button type="button" data-tab="tab-image"><b>5</b> Görseller</button>'
  +'</div></div>';
  $body.prepend(html);
}

function simplify(){
 setTabLabel('tab-general','Temel Bilgiler'); setTabLabel('tab-data','Ürün / SEO');
 setTabLabel('tab-links','Kategori'); setTabLabel('tab-attribute','Teknik Özellikler'); setTabLabel('tab-image','Görseller');
 ['tab-option','tab-recurring','tab-discount','tab-special','tab-reward','tab-design'].forEach(hideTab);
 ['sku','upc','ean','jan','isbn','mpn','location','quantity','minimum','subtract','stock_status_id','shipping','weight','weight_class_id','length_class_id'].forEach(hideField);
 $('[name="manufacturer"]').closest('.form-group').addClass('egeser-advanced-only');
 $('#input-filter').closest('.form-group').addClass('egeser-advanced-only');
 $('#input-download').closest('.form-group').addClass('egeser-advanced-only');
}

function addSpecsHelper(){
 var $tab=$('#tab-attribute'); if(!$tab.length||$('#egeser-spec-helper').length)return;
 var rows=specs.map(function(s){return '<tr><td><strong>'+s[0]+'</strong></td><td>'+s[1]+'</td></tr>';}).join('');
 $tab.prepend('<div id="egeser-spec-helper" class="egeser-spec-helper"><div class="egeser-spec-helper__head">'
 +'<span>TEKNİK ÖZELLİK REHBERİ</span><h3>Standart prefabrik alanları</h3>'
 +'<p>V2.1 güvenlik nedeniyle attribute veya veritabanı kaydı oluşturmaz. Bu liste, mevcut Özellikler tablosunda kullanacağımız alanları gösterir.</p></div>'
 +'<div class="table-responsive"><table class="table table-bordered table-hover"><thead><tr><th>Özellik</th><th>Örnek / Açıklama</th></tr></thead><tbody>'+rows+'</tbody></table></div>'
 +'<div class="egeser-spec-note"><i class="fa fa-shield"></i> Admin-only güvenli sürüm.</div></div>');
}

function setMode(advanced){
 $('body').toggleClass('egeser-product-advanced',!!advanced);
 var $b=$('#egeser-toggle-advanced');
 $b.html(advanced?'<i class="fa fa-compress"></i> Sade Moda Dön':'<i class="fa fa-sliders"></i> Gelişmiş Modu Aç');
 try{localStorage.setItem('egeserProductAdvancedV21',advanced?'1':'0');}catch(e){}
}

$(function(){
 addTopPanel(); simplify(); addSpecsHelper();
 $(document).on('click','.egeser-v21-shortcuts button',function(){var id=$(this).data('tab'),$a=$('a[href="#'+id+'"]');if($a.length)$a.tab('show');});
 $(document).on('click','#egeser-toggle-advanced',function(){setMode(!$('body').hasClass('egeser-product-advanced'));});
 var adv=false; try{adv=localStorage.getItem('egeserProductAdvancedV21')==='1';}catch(e){}
 setMode(adv);
});
})(window.jQuery);
