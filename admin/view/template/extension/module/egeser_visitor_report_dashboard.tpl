<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid">
    <h1><?php echo $heading_title; ?></h1>
    <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
  </div></div>

  <div class="container-fluid">
    <?php include(DIR_TEMPLATE . 'extension/module/egeser_visitor_report_tabs.tpl'); ?>

    <div class="row">
      <div class="col-sm-3 col-lg-2"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo (int)$kpis['visitors']; ?></h2><small>Ziyaretçi</small></div></div></div>
      <div class="col-sm-3 col-lg-2"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo (int)$kpis['sessions']; ?></h2><small>Oturum</small></div></div></div>
      <div class="col-sm-3 col-lg-2"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo (int)$kpis['product_views']; ?></h2><small>Ürün Görüntüleme</small></div></div></div>
      <div class="col-sm-3 col-lg-2"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo (int)$kpis['whatsapp_clicks']; ?></h2><small>WhatsApp</small></div></div></div>
      <div class="col-sm-3 col-lg-2"><div class="panel panel-default"><div class="panel-body text-center"><h2><?php echo (int)$kpis['phone_clicks']; ?></h2><small>Telefon</small></div></div></div>
      <div class="col-sm-3 col-lg-2"><div class="panel panel-success"><div class="panel-body text-center"><h2><?php echo (int)$kpis['conversions']; ?></h2><small>Dönüşüm (%<?php echo $kpis['conversion_rate']; ?>)</small></div></div></div>
    </div>

    <div class="row">
      <div class="col-sm-4">
        <div class="panel panel-default">
          <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-cube"></i> En Çok Bakılan Ürünler</h3></div>
          <table class="table table-hover">
            <?php if ($top_products) { foreach ($top_products as $p) { ?>
            <tr><td><?php echo htmlspecialchars($p['name'] ?: ('#' . $p['entity_id']), ENT_QUOTES, 'UTF-8'); ?></td><td class="text-right"><?php echo (int)$p['views']; ?></td></tr>
            <?php } } else { ?><tr><td class="text-center text-muted">Veri yok.</td></tr><?php } ?>
          </table>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="panel panel-default">
          <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-share-alt"></i> En Çok Trafik Getiren Kaynaklar</h3></div>
          <table class="table table-hover">
            <?php if ($top_sources) { foreach ($top_sources as $s) { ?>
            <tr><td><?php echo htmlspecialchars(ucfirst($s['source']), ENT_QUOTES, 'UTF-8'); ?></td><td class="text-right"><?php echo (int)$s['sessions']; ?></td></tr>
            <?php } } else { ?><tr><td class="text-center text-muted">Veri yok.</td></tr><?php } ?>
          </table>
        </div>
      </div>
      <div class="col-sm-4">
        <div class="panel panel-default">
          <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-check-circle"></i> Son 10 Dönüşüm</h3></div>
          <table class="table table-hover">
            <?php if ($recent_conversions) { foreach ($recent_conversions as $c) { ?>
            <tr>
              <td>#<?php echo strtoupper(substr((string)$c['visitor_token'], 0, 6)); ?></td>
              <td><?php echo htmlspecialchars($c['conversion_type'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td class="text-right text-muted"><small><?php echo $c['created_at']; ?></small></td>
            </tr>
            <?php } } else { ?><tr><td class="text-center text-muted">Henüz dönüşüm yok.</td></tr><?php } ?>
          </table>
        </div>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-circle text-success"></i> Aktif Ziyaretçiler (<?php echo count($live_visitors); ?>)</h3></div>
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead><tr><th>Ziyaretçi</th><th>Kaynak</th><th>Şu An Baktığı Sayfa</th><th>Cihaz</th><th>Sayfa Gör.</th></tr></thead>
          <tbody>
          <?php if ($live_visitors) { foreach ($live_visitors as $v) { ?>
            <tr>
              <td>#<?php echo strtoupper(substr((string)$v['visitor_token'], 0, 6)); ?></td>
              <td><?php echo htmlspecialchars(ucfirst($v['source']), ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($v['current_page'] ?: $v['current_url'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($v['device_type'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo (int)$v['pageviews']; ?></td>
            </tr>
          <?php } } else { ?><tr><td colspan="5" class="text-center text-muted">Şu anda aktif ziyaretçi yok.</td></tr><?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
