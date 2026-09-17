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
      + '      <div class="egeser-product-panel__eyebrow">EGESER PREFABRİK ÜRÜN YÖNETİMİ V2</div>'
      + '      <h3>Hızlı Ürün Girişi</h3>'
      + '      <p>Prefabrik ürün için gerekli alanları doldurun. Standart e-ticaret alanları gizlenir; gerektiğinde Gelişmiş Mod ile açılır.</p>'
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
      + '  <div class="egeser-product-tip"><i class="fa fa-info-circle"></i> Önerilen sıra: Temel Bilgiler → Ürün/SEO → Kategori → Teknik Özellikler → Görseller.</div>'
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
  }

  function simplifyLinks() {
    ['manufacturer_id'].forEach(hideField);
    $('[name="manufacturer"]').closest('.form-group').addClass('egeser-advanced-only');
    $('#input-filter').closest('.form-group').addClass('egeser-advanced-only');
    $('#input-download').closest('.form-group').addClass('egeser-advanced-only');
  }

  function setMode(advanced) {
    $('body').toggleClass('egeser-product-advanced', !!advanced);
    var $btn = $('#egeser-toggle-advanced');

    if (advanced) {
      $btn.html('<i class="fa fa-compress"></i> Sade Moda Dön');
    } else {
      $btn.html('<i class="fa fa-sliders"></i> Gelişmiş Modu Aç');
    }

    try { localStorage.setItem('egeserProductAdvancedV2', advanced ? '1' : '0'); } catch(e){}
  }

  function prepareEmptyFixedAttributesBeforeSubmit() {
    $('#form-product').on('submit.egeserV2', function(){
      $('#egeser-prefabrik-specs .egeser-prefabrik-field').each(function(){
        var $field = $(this);
        var hasValue = false;

        $field.find('.egeser-fixed-attribute-text').each(function(){
          if ($.trim($(this).val()) !== '') hasValue = true;
        });

        // Native OpenCart can safely receive only the attributes that have a value.
        $field.find('input').prop('disabled', !hasValue);
      });
    });
  }

  function bindEvents() {
    $(document).on('click', '.egeser-shortcut', function(){
      var id = $(this).data('tab');
      var $a = $('a[href="#' + id + '"]');

      if ($a.length) {
        $a.tab('show');
        var top = $('.nav-tabs').first().offset();
        if (top) $('html,body').animate({scrollTop: top.top - 20}, 180);
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
    prepareEmptyFixedAttributesBeforeSubmit();

    var advanced = false;
    try { advanced = localStorage.getItem('egeserProductAdvancedV2') === '1'; } catch(e){}
    setMode(advanced);
  });
})(window.jQuery);
