<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid"><h1><?php echo $heading_title; ?></h1><ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul></div></div>
  <div class="container-fluid">
    <div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title"><i class="fa fa-sitemap"></i> <?php echo $text_edit; ?></h3></div><div class="panel-body">
      <div class="alert alert-info"><?php echo $text_info; ?></div>
      <?php if (!empty($result)) { foreach ($result as $r) { ?><div class="alert alert-<?php echo $r['status']; ?>"><?php echo htmlspecialchars($r['message'], ENT_QUOTES, 'UTF-8'); ?></div><?php } } ?>
      <div class="table-responsive"><table class="table table-bordered table-hover"><thead><tr><th>Tür</th><th>Ad</th><th>SEO URL</th><th>Durum</th><th>Eşleşme</th></tr></thead><tbody>
      <?php foreach ($status_rows as $row) { ?><tr><td><?php echo $row['type']; ?></td><td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td><td><code><?php echo htmlspecialchars($row['slug'], ENT_QUOTES, 'UTF-8'); ?></code></td><td><?php echo $row['exists'] ? '<span class="label label-success">Mevcut</span>' : '<span class="label label-warning">Eksik</span>'; ?></td><td><?php echo htmlspecialchars($row['query'], ENT_QUOTES, 'UTF-8'); ?></td></tr><?php } ?>
      </tbody></table></div>
      <form method="post" action="<?php echo htmlspecialchars($apply_url, ENT_QUOTES, 'UTF-8'); ?>" onsubmit="return confirm('Eksik kategori ve sayfalar oluşturulsun mu? Mevcut eşleşen kayıtlar korunacaktır.');">
        <div class="checkbox"><label><input type="checkbox" name="confirm" value="1" required> Mevcut kayıtların korunacağını ve yalnız eksik kayıtların oluşturulacağını onaylıyorum.</label></div>
        <button type="submit" class="btn btn-primary"><i class="fa fa-magic"></i> <?php echo $button_apply; ?></button>
        <a href="<?php echo $cancel; ?>" class="btn btn-default"><?php echo $button_cancel; ?></a>
      </form>
    </div></div>
  </div>
</div>
<?php echo $footer; ?>
