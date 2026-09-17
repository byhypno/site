(function($){
  'use strict';

  function tabLink(id) {
    return $('a[href="#' + id + '"]');
  }

  function setTabLabel(id, label) {
    var $a = tabLink(id);
    if ($a.length) $a.text(label);
  }

  function hideTab(id) {
    var $a = tabLink(id);
    if ($a.length) $a.closest('li').addClass('egeser-advanced-only');
  }

  function fieldRowByName(name) {
    var $el = $('[name="' + name + '"]');
    if (!$el.length) return $();
    return $el.first().closest('.form-group');
  }

  function hideField(name) {
    var $row = fieldRowByName(name);
    if ($row.length) $row.addClass('egeser-advanced-only');
  }

  function addQuickPanel() {
    var $panelBody = $('#form-product .panel-body').first();
    if (!$panelBody.length || $('#egeser-prefabrik-admin').length) return;

    var html = ''
      + '<div id="egeser-prefabrik-admin" class="egeser-product-panel">'
      + '  <div class="egeser-product-panel__head">'
      + '    <div>'
      + '      <div class="egeser-product-panel__eyebrow">EGESER PREFABRİK ÜRÜN YÖNETİMİ</div>'
      + '      <h3>Hızlı Ürün Girişi</h3>'
      + '      <p>Prefabrik ürün eklerken gerekli alanlara odaklanın. Gizlenen alanlar silinmez; Gelişmiş Mod ile tekrar açılabilir.</p>'
      + '    </div>'
      + '    <button type="button" class="btn btn-default" id="egeser-toggle-advanced">'
      + '      <i class="fa fa-sliders"></i> Gelişmiş Modu Aç'
      + '    </button>'
      + '  </div>'
      + '  <div class="egeser-product-shortcuts">'
      + '    <button type="button" class="egeser-shortcut" data-tab="tab-general"><strong>1</strong><span>Temel Bilgiler</span></button>'
      + '    <button type="button" class="egeser-shortcut" data-tab="tab-data"><strong>2</strong><span>Ürün / SEO</span></button>'
      + '    <button type="button" class="egeser-shortcut" data-tab="tab-links"><strong>3</strong><span>Kategori</span></button>'
      + '    <button type="button" class="egeser-shortcut" data-tab="tab-attribute"><strong>4</strong><span>Teknik Özellikler</span></button>'
      + '    <button type="button" class="egeser-shortcut" data-tab="tab-image"><strong>5</strong><span>Görseller</span></button>'
      + '  </div>'
      + '  <div class="egeser-product-tip"><i class="fa fa-info-circle"></i> Önerilen sıra: Ürün adı → açıklama/SEO → model/SEO URL → kategori → teknik özellikler → görseller.</div>'
      + '</div>';

    $panelBody.prepend(html);
  }

  function simplifyTabs() {
    setTabLabel('tab-general', 'Temel Bilgiler');
    setTabLabel('tab-data', 'Ürün / SEO');
    setTabLabel('tab-links', 'Kategori');
    setTabLabel('tab-attribute', 'Teknik Özellikler');
    setTabLabel('tab-image', 'Görseller');
    setTabLabel('tab-design', 'Tasarım');

    ['tab-option','tab-recurring','tab-discount','tab-special','tab-reward','tab-design'].forEach(hideTab);
  }

  function simplifyDataFields() {
    [
      'sku','upc','ean','jan','isbn','mpn','location',
      'quantity','minimum','subtract','stock_status_id',
      'shipping','weight','weight_class_id','length_class_id'
    ].forEach(hideField);

    // Model, Price, SEO URL, Date Available and Dimensions stay visible.
  }

  function simplifyLinks() {
    // Keep Category and Related Products visible.
    ['manufacturer_id'].forEach(hideField);
    $('[name="product_download[]"]').closest('.form-group').addClass('egeser-advanced-only');
    $('[name="filter"]').closest('.form-group').addClass('egeser-advanced-only');
  }

  function setMode(advanced) {
    $('body').toggleClass('egeser-product-advanced', !!advanced);
    var $btn = $('#egeser-toggle-advanced');
    if (!$btn.length) return;
    if (advanced) {
      $btn.html('<i class="fa fa-compress"></i> Sade Moda Dön');
      try { localStorage.setItem('egeserProductAdvanced', '1'); } catch(e){}
    } else {
      $btn.html('<i class="fa fa-sliders"></i> Gelişmiş Modu Aç');
      try { localStorage.setItem('egeserProductAdvanced', '0'); } catch(e){}
    }
  }

  function bindEvents() {
    $(document).on('click', '.egeser-shortcut', function(){
      var id = $(this).data('tab');
      var $a = $('a[href="#' + id + '"]');
      if ($a.length) {
        $a.tab('show');
        $('html,body').animate({scrollTop: $('.nav-tabs').first().offset().top - 20}, 180);
      }
    });

    $(document).on('click', '#egeser-toggle-advanced', function(){
      setMode(!$('body').hasClass('egeser-product-advanced'));
    });
  }

  $(function(){
    addQuickPanel();
    simplifyTabs();
    simplifyDataFields();
    simplifyLinks();
    bindEvents();

    var advanced = false;
    try { advanced = localStorage.getItem('egeserProductAdvanced') === '1'; } catch(e){}
    setMode(advanced);
  });
})(window.jQuery);
