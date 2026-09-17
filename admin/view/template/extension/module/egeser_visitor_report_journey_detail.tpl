<?php
$eg_event_labels = array(
    'page_view' => 'Sayfa görüntüledi',
    'product_view' => 'Ürün inceledi',
    'category_view' => 'Kategori inceledi',
    'whatsapp_click' => 'WhatsApp butonuna tıkladı',
    'phone_click' => 'Telefon numarasına tıkladı',
    'quote_form_start' => 'Teklif formunu doldurmaya başladı',
    'quote_form_submit' => 'Teklif formu gönderdi',
    'contact_form_submit' => 'İletişim formu gönderdi',
    'map_click' => 'Haritada açtı',
    'brochure_click' => 'E-Katalog/broşür açtı'
);
$eg_event_icons = array(
    'page_view' => 'fa-file-o', 'product_view' => 'fa-cube', 'category_view' => 'fa-th-large',
    'whatsapp_click' => 'fa-whatsapp', 'phone_click' => 'fa-phone', 'quote_form_start' => 'fa-pencil',
    'quote_form_submit' => 'fa-check-circle', 'contact_form_submit' => 'fa-envelope',
    'map_click' => 'fa-map-marker', 'brochure_click' => 'fa-download'
);
?>
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid">
    <h1><?php echo $heading_title; ?> - Ziyaretçi Yolculuğu</h1>
    <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
  </div></div>

  <div class="container-fluid">
    <?php if (!$session) { ?>
      <div class="alert alert-danger">Oturum bulunamadı.</div>
    <?php } else { ?>

    <div class="row">
      <div class="col-sm-8">
        <div class="panel panel-default">
          <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-road"></i> Zaman Çizelgesi</h3></div>
          <div class="panel-body">
            <?php if ($timeline) { ?>
            <ul class="list-unstyled" style="border-left:2px solid #eee;margin-left:10px;padding-left:20px;">
              <?php foreach ($timeline as $ev) { ?>
              <li style="margin-bottom:18px;position:relative;">
                <span style="position:absolute;left:-27px;width:14px;height:14px;border-radius:50%;background:#1065D2;display:inline-block;top:3px;"></span>
                <strong><?php echo date('H:i', strtotime($ev['created_at'])); ?></strong>
                &nbsp;<i class="fa <?php echo isset($eg_event_icons[$ev['event_type']]) ? $eg_event_icons[$ev['event_type']] : 'fa-circle'; ?>"></i>
                <?php echo isset($eg_event_labels[$ev['event_type']]) ? $eg_event_labels[$ev['event_type']] : htmlspecialchars($ev['event_type'], ENT_QUOTES, 'UTF-8'); ?>
                <?php if ($ev['entity_type'] === 'product' && $ev['entity_id']) { ?>
                  <span class="text-muted">(Ürün #<?php echo (int)$ev['entity_id']; ?>)</span>
                <?php } ?>
                <?php if ($ev['page_title']) { ?>
                  <div class="text-muted"><small><?php echo htmlspecialchars($ev['page_title'], ENT_QUOTES, 'UTF-8'); ?></small></div>
                <?php } ?>
              </li>
              <?php } ?>
            </ul>
            <?php } else { ?>
              <p class="text-muted">Bu oturum için kayıtlı olay yok.</p>
            <?php } ?>
          </div>
        </div>
      </div>

      <div class="col-sm-4">
        <div class="panel panel-default">
          <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-info-circle"></i> Özet</h3></div>
          <div class="panel-body">
            <p><strong>Ziyaretçi:</strong> #<?php echo strtoupper(substr((string)$session['visitor_token'], 0, 6)); ?></p>
            <p><strong>İlk Kaynak:</strong> <?php echo htmlspecialchars(ucfirst($session['first_source']), ENT_QUOTES, 'UTF-8'); ?> / <?php echo htmlspecialchars($session['first_medium'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>İlk Ziyaret:</strong> <?php echo $session['first_seen']; ?></p>
            <p><strong>Toplam Ziyaret (Oturum):</strong> <?php echo (int)$visitor_session_count; ?></p>
            <p><strong>Bu Oturumun Kaynağı:</strong> <?php echo htmlspecialchars(ucfirst($session['source']), ENT_QUOTES, 'UTF-8'); ?> / <?php echo htmlspecialchars($session['medium'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Cihaz:</strong> <?php echo htmlspecialchars($session['device_type'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($session['browser'], ENT_QUOTES, 'UTF-8'); ?> / <?php echo htmlspecialchars($session['os'], ENT_QUOTES, 'UTF-8'); ?>)</p>
            <p><strong>Sayfa Görüntüleme:</strong> <?php echo (int)$session['pageviews']; ?></p>
            <hr>
            <?php if ($lead) { ?>
              <p class="text-success"><i class="fa fa-check-circle"></i> <strong>Lead #<?php echo (int)$lead['lead_id']; ?> ile eşleşti</strong></p>
              <p><?php echo htmlspecialchars($lead['name'], ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($lead['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
              <p><span class="label label-info"><?php echo htmlspecialchars($lead['lead_status'], ENT_QUOTES, 'UTF-8'); ?></span></p>
            <?php } else { ?>
              <p class="text-muted"><i class="fa fa-minus-circle"></i> Lead: Henüz yok</p>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</div>
<?php echo $footer; ?>
