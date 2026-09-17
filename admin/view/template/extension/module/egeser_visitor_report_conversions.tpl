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
            <th>Tarih</th>
            <th>Ziyaretçi</th>
            <th>Tür</th>
            <th>Kaynak</th>
            <th>Kampanya</th>
            <th>Lead</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($conversions) { foreach ($conversions as $c) { ?>
          <tr>
            <td><small><?php echo $c['created_at']; ?></small></td>
            <td>#<?php echo strtoupper(substr((string)$c['visitor_token'], 0, 6)); ?></td>
            <td><?php echo htmlspecialchars($c['conversion_type'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars(ucfirst($c['source']), ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($c['campaign'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo $c['lead_id'] ? ('#' . (int)$c['lead_id']) : '-'; ?></td>
          </tr>
        <?php } } else { ?>
          <tr><td colspan="6" class="text-center text-muted">Bu tarih aralığında dönüşüm yok.</td></tr>
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
