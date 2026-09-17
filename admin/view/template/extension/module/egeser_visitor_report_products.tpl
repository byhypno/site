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
            <th>Ürün</th>
            <th>Görüntülenme</th>
            <th>Ziyaretçi</th>
            <th>WhatsApp</th>
            <th>Telefon</th>
            <th>Teklif</th>
            <th>Dönüşüm %</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($products) { foreach ($products as $p) { ?>
          <tr>
            <td><?php echo htmlspecialchars($p['name'] ?: ('#' . $p['product_id']), ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo (int)$p['views']; ?></td>
            <td><?php echo (int)$p['visitors']; ?></td>
            <td><?php echo (int)$p['whatsapp_clicks']; ?></td>
            <td><?php echo (int)$p['phone_clicks']; ?></td>
            <td><?php echo (int)$p['quote_forms']; ?></td>
            <td><?php echo $p['conversion_rate']; ?>%</td>
          </tr>
        <?php } } else { ?>
          <tr><td colspan="7" class="text-center text-muted">Bu tarih aralığında ürün görüntüleme kaydı yok.</td></tr>
        <?php } ?>
        </tbody>
      </table>
    </div>

    <?php if ($pagination['pages'] > 1) { ?>
    <ul class="pager">
      <?php if ($pagination['prev']) { ?><li><a href="<?php echo $pagination['prev']; ?>">&larr; Önceki</a></li><?php } ?>
      <li class="disabled"><a href="#"><?php echo $pagination['page']; ?> / <?php echo $pagination['pages']; ?></a></li>
      <?php if ($pagination['next']) { ?><li><a href="<?php echo $pagination['next']; ?>">Sonraki &rarr;</a></li><?php } ?>
    </ul>
    <?php } ?>
  </div>
</div>
<?php echo $footer; ?>
