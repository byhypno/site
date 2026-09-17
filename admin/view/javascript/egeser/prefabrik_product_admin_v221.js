(function(){
'use strict';

function boot(){
  var $ = window.jQuery;
  if (!$) { setTimeout(boot, 50); return; }

  if ($('#egeser-v221-ready').length) return;

  var specs = [
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

  function tab(id){ return $('a[href="#'+id+'"]'); }
  function rename(id,label){ var $a=tab(id); if($a.length){$a.text(label);} }
  function advTab(id){ var $a=tab(id); if($a.length){$a.closest('li').addClass('egeser-v221-advanced');} }
  function advField(name){
    var $el=$('[name="'+name+'"]').first();
    if($el.length){$el.closest('.form-group').addClass('egeser-v221-advanced');}
  }

  var $body = $('#form-product .panel-body').first();
  if (!$body.length) return;

  $body.prepend(
    '<div id="egeser-v221-ready" class="egeser-v221-header">'+
      '<div><small>EGESER PREFABRİK ÜRÜN YÖNETİMİ V2.2.1</small>'+
      '<strong>Hızlı Ürün Girişi</strong>'+
      '<span>5 ana bölüm ile sade ürün yönetimi</span></div>'+
      '<button type="button" id="egeser-v221-toggle" class="btn btn-default"><i class="fa fa-sliders"></i> Gelişmiş Mod</button>'+
    '</div>'
  );

  rename('tab-general','Temel Bilgiler');
  rename('tab-data','Ürün / SEO');
  rename('tab-links','Kategori');
  rename('tab-attribute','Teknik Özellikler');
  rename('tab-image','Görseller');

  ['tab-option','tab-recurring','tab-discount','tab-special','tab-reward','tab-design'].forEach(advTab);
  ['sku','upc','ean','jan','isbn','mpn','location','quantity','minimum','subtract','stock_status_id',
   'shipping','weight','weight_class_id','length_class_id'].forEach(advField);

  $('[name="manufacturer"]').closest('.form-group').addClass('egeser-v221-advanced');
  $('#input-filter').closest('.form-group').addClass('egeser-v221-advanced');
  $('#input-download').closest('.form-group').addClass('egeser-v221-advanced');

  var $attr = $('#tab-attribute');
  if ($attr.length && !$('#egeser-v221-guide').length) {
    var rows='';
    $.each(specs,function(_,s){
      rows += '<tr><td><strong>'+s[0]+'</strong></td><td>'+s[1]+'</td></tr>';
    });

    $attr.prepend(
      '<div id="egeser-v221-guide" class="egeser-v221-guide">'+
        '<div class="egeser-v221-guide-head"><strong>Standart Prefabrik Teknik Özellikleri</strong>'+
        '<span>Bu alanları ürünlerimizde standartlaştıracağız.</span></div>'+
        '<div class="table-responsive"><table class="table table-bordered">'+
        '<thead><tr><th>Özellik</th><th>Örnek / Açıklama</th></tr></thead>'+
        '<tbody>'+rows+'</tbody></table></div>'+
      '</div>'
    );
  }

  function setMode(on){
    $('#form-product').toggleClass('egeser-v221-show-advanced', !!on);
    $('#egeser-v221-toggle').html(on
      ? '<i class="fa fa-compress"></i> Sade Moda Dön'
      : '<i class="fa fa-sliders"></i> Gelişmiş Mod');
    try { localStorage.setItem('egeserV221Advanced', on ? '1' : '0'); } catch(e){}
  }

  var on=false;
  try { on=localStorage.getItem('egeserV221Advanced')==='1'; } catch(e){}
  setMode(on);

  $(document).off('click.egeserV221','#egeser-v221-toggle')
    .on('click.egeserV221','#egeser-v221-toggle',function(){
      setMode(!$('#form-product').hasClass('egeser-v221-show-advanced'));
    });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', boot);
} else {
  boot();
}
})();
