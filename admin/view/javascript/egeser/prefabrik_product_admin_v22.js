(function($){
'use strict';

var specs = [
  ['Alan','Örn: 85 m²'],
  ['Oda Sayısı','Örn: 2+1'],
  ['Kat Sayısı','Örn: Tek Katlı'],
  ['Yapı Tipi','Örn: Prefabrik Ev'],
  ['Veranda','Örn: Var / Yok / 14 m²'],
  ['Dış Cephe','Dış cephe sistemini yazın'],
  ['İç Duvar','İç duvar sistemini yazın'],
  ['Duvar Kalınlığı','Örn: 10 cm'],
  ['Çatı Sistemi','Çatı sistemini yazın'],
  ['Tavan','Tavan uygulamasını yazın'],
  ['Zemin','Zemin uygulamasını yazın'],
  ['PVC Doğrama','Örn: Isıcamlı PVC doğrama'],
  ['Cam Sistemi','Örn: Isıcam'],
  ['İç Kapılar','İç kapı özelliklerini yazın'],
  ['Dış Kapı','Dış kapı özelliklerini yazın'],
  ['Mutfak','Mutfak kapsamını yazın'],
  ['Banyo','Banyo kapsamını yazın'],
  ['Elektrik Tesisatı','Elektrik tesisatı kapsamını yazın'],
  ['Sıhhi Tesisat','Sıhhi tesisat kapsamını yazın'],
  ['Isı Yalıtımı','Isı yalıtımı sistemini yazın']
];

function tab(id){ return $('a[href="#'+id+'"]'); }
function rename(id,label){ var $a=tab(id); if($a.length){$a.text(label);} }
function markAdvanced(id){ var $a=tab(id); if($a.length){$a.closest('li').addClass('egeser-v22-advanced');} }
function markField(name){
  var $el=$('[name="'+name+'"]').first();
  if($el.length){$el.closest('.form-group').addClass('egeser-v22-advanced');}
}

function addHeader(){
  var $body=$('#form-product .panel-body').first();
  if(!$body.length || $('#egeser-v22-header').length) return;

  var html = ''
    + '<div id="egeser-v22-header" class="egeser-v22-header">'
    + ' <div class="egeser-v22-header__copy">'
    + '  <div class="egeser-v22-kicker">EGESER PREFABRİK ÜRÜN YÖNETİMİ</div>'
    + '  <strong>Hızlı Ürün Girişi</strong>'
    + '  <span>Temel bilgilerden görsellere kadar 5 adımda ürün ekleyin.</span>'
    + ' </div>'
    + ' <button type="button" class="btn btn-default" id="egeser-v22-toggle"><i class="fa fa-sliders"></i> Gelişmiş Mod</button>'
    + '</div>';
  $body.prepend(html);
}

function addGuide(){
  var $tab=$('#tab-attribute');
  if(!$tab.length || $('#egeser-v22-guide').length) return;
  var rows='';
  $.each(specs,function(_,s){
    rows += '<tr><td>'+s[0]+'</td><td>'+s[1]+'</td></tr>';
  });
  var html=''
    +'<div id="egeser-v22-guide" class="egeser-v22-guide">'
    +'<div class="egeser-v22-guide__head"><strong>Standart Prefabrik Teknik Özellikleri</strong>'
    +'<span>Şimdilik rehber olarak gösterilir. Mevcut OpenCart Özellikler alanına aynı adlarla ekleyebiliriz.</span></div>'
    +'<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Özellik</th><th>Örnek / Açıklama</th></tr></thead><tbody>'+rows+'</tbody></table></div>'
    +'</div>';
  $tab.prepend(html);
}

function setup(){
  rename('tab-general','Temel Bilgiler');
  rename('tab-data','Ürün / SEO');
  rename('tab-links','Kategori');
  rename('tab-attribute','Teknik Özellikler');
  rename('tab-image','Görseller');

  ['tab-option','tab-recurring','tab-discount','tab-special','tab-reward','tab-design'].forEach(markAdvanced);
  ['sku','upc','ean','jan','isbn','mpn','location','quantity','minimum','subtract','stock_status_id','shipping','weight','weight_class_id','length_class_id'].forEach(markField);

  $('[name="manufacturer"]').closest('.form-group').addClass('egeser-v22-advanced');
  $('#input-filter').closest('.form-group').addClass('egeser-v22-advanced');
  $('#input-download').closest('.form-group').addClass('egeser-v22-advanced');

  addHeader();
  addGuide();
}

function setAdvanced(on){
  $('#form-product').toggleClass('egeser-v22-show-advanced',!!on);
  $('#egeser-v22-toggle').html(on
    ? '<i class="fa fa-compress"></i> Sade Moda Dön'
    : '<i class="fa fa-sliders"></i> Gelişmiş Mod');
  try{localStorage.setItem('egeserV22Advanced',on?'1':'0');}catch(e){}
}

$(function(){
  setup();
  var on=false;
  try{on=localStorage.getItem('egeserV22Advanced')==='1';}catch(e){}
  setAdvanced(on);

  $(document).on('click','#egeser-v22-toggle',function(){
    setAdvanced(!$('#form-product').hasClass('egeser-v22-show-advanced'));
  });
});
})(window.jQuery);
