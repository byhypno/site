<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid">
    <h1><?php echo $heading_title; ?></h1>
    <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
  </div></div>

  <div class="container-fluid">
    <?php include(DIR_TEMPLATE . 'extension/module/egeser_visitor_report_tabs.tpl'); ?>

    <p><a href="<?php echo $toggle_converted_url; ?>" class="btn btn-<?php echo $only_converted ? 'success' : 'default'; ?> btn-sm"><i class="fa fa-<?php echo $only_converted ? 'check-square-o' : 'square-o'; ?>"></i> Sadece dönüşenler</a></p>

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Ziyaretçi</th>
            <th>İlk Kaynak</th>
            <th>Giriş Sayfası</th>
            <th>Cihaz</th>
            <th>Sayfa Gör.</th>
            <th>Başlangıç</th>
            <th>Son Hareket</th>
            <th>Dönüşüm</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        <?php if ($journeys) { foreach ($journeys as $j) { ?>
          <tr>
            <td>#<?php echo strtoupper(substr((string)$j['visitor_token'], 0, 6)); ?></td>
            <td><?php echo htmlspecialchars(ucfirst($j['source']), ENT_QUOTES, 'UTF-8'); ?></td>
            <td style="max-width:220px;word-break:break-word;"><small><?php echo htmlspecialchars($j['landing_page'], ENT_QUOTES, 'UTF-8'); ?></small></td>
            <td><?php echo htmlspecialchars($j['device_type'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo (int)$j['pageviews']; ?></td>
            <td><small><?php echo $j['started_at']; ?></small></td>
            <td><small><?php echo $j['last_activity']; ?></small></td>
            <td><?php echo $j['converted'] ? '<span class="label label-success">Evet</span>' : '<span class="label label-default">Hayır</span>'; ?></td>
            <td><a href="<?php echo $j['detail_url']; ?>" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> Gör</a></td>
          </tr>
        <?php } } else { ?>
          <tr><td colspan="9" class="text-center text-muted">Bu tarih aralığında ziyaretçi yolculuğu bulunamadı.</td></tr>
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
