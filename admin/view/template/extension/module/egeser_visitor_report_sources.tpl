<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid">
    <h1><?php echo $heading_title; ?></h1>
    <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
  </div></div>

  <div class="container-fluid">
    <?php include(DIR_TEMPLATE . 'extension/module/egeser_visitor_report_tabs.tpl'); ?>

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Kaynak</th>
            <th>Ziyaretçi</th>
            <th>Oturum</th>
            <th>Sayfa Görüntüleme</th>
            <th>Ürün Görüntüleme</th>
            <th>WhatsApp</th>
            <th>Dönüşüm</th>
            <th>Dönüşüm %</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($sources) { foreach ($sources as $s) { ?>
          <tr>
            <td><?php echo htmlspecialchars(ucfirst($s['source']), ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo (int)$s['visitors']; ?></td>
            <td><?php echo (int)$s['sessions']; ?></td>
            <td><?php echo (int)$s['pageviews']; ?></td>
            <td><?php echo (int)$s['product_views']; ?></td>
            <td><?php echo (int)$s['whatsapp_clicks']; ?></td>
            <td><?php echo (int)$s['conversions']; ?></td>
            <td><?php echo $s['conversion_rate']; ?>%</td>
          </tr>
        <?php } } else { ?>
          <tr><td colspan="8" class="text-center text-muted">Bu tarih aralığında trafik kaydı yok.</td></tr>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php echo $footer; ?>
