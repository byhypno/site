<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $template_url; ?>" data-toggle="tooltip" title="<?php echo $button_template; ?>" class="btn btn-default"><i class="fa fa-download"></i> <?php echo $button_template; ?></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php } ?>
    <?php if ($success) { ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-upload"></i> Dosya Yükle</h3></div>
      <div class="panel-body">
        <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $text_template_help; ?><br><strong>CSV:</strong> her sunucuda desteklenir. &nbsp; <strong>XLSX:</strong> <?php echo $xlsx_available ? '<span class="text-success">aktif</span>' : '<span class="text-danger">PHP ZipArchive kapalı; CSV kullanın</span>'; ?><br><strong>Kategori ayırıcı:</strong> <code>|</code> &nbsp; <strong>Ek görsel ayırıcı:</strong> <code>|</code> &nbsp; <strong>Özellik sütunu:</strong> <code>ozellik:Toplam Alan</code></div>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal" id="form-preview">
          <input type="hidden" name="mode" value="preview">
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_file; ?></label>
            <div class="col-sm-10"><input type="file" name="import_file" accept=".xlsx,.csv" required class="form-control"></div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_allow_update; ?></label>
            <div class="col-sm-10"><label class="checkbox-inline"><input type="checkbox" name="allow_update" value="1" <?php echo $allow_update ? 'checked="checked"' : ''; ?>> Evet</label><p class="help-block"><?php echo $help_allow_update; ?></p></div>
          </div>
          <div class="form-group"><div class="col-sm-offset-2 col-sm-10"><button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> <?php echo $button_preview; ?></button></div></div>
        </form>
      </div>
    </div>

    <?php if (!empty($summary)) { ?>
    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-check-square-o"></i> <?php echo $text_summary; ?></h3></div>
      <div class="panel-body">
        <div class="row text-center" style="margin-bottom:15px">
          <div class="col-sm-2"><div class="well"><strong><?php echo (int)$summary['rows']; ?></strong><br><?php echo $text_rows; ?></div></div>
          <div class="col-sm-2"><div class="well"><strong><?php echo (int)$summary['creates']; ?></strong><br><?php echo $text_creates; ?></div></div>
          <div class="col-sm-2"><div class="well"><strong><?php echo (int)$summary['updates']; ?></strong><br><?php echo $text_updates; ?></div></div>
          <div class="col-sm-2"><div class="well"><strong><?php echo (int)$summary['skips']; ?></strong><br><?php echo $text_skips; ?></div></div>
          <div class="col-sm-2"><div class="well"><strong style="color:#a94442"><?php echo (int)$summary['errors']; ?></strong><br><?php echo $text_errors; ?></div></div>
          <div class="col-sm-2"><div class="well"><strong style="color:#8a6d3b"><?php echo (int)$summary['warnings']; ?></strong><br><?php echo $text_warnings; ?></div></div>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-hover table-condensed">
            <thead><tr><th><?php echo $column_row; ?></th><th><?php echo $column_action; ?></th><th><?php echo $column_model; ?></th><th><?php echo $column_name; ?></th><th><?php echo $column_categories; ?></th><th><?php echo $column_keyword; ?></th><th><?php echo $column_price; ?></th><th><?php echo $column_status; ?></th><th><?php echo $column_notes; ?></th></tr></thead>
            <tbody>
            <?php foreach ($preview as $r) { ?>
              <tr class="<?php echo $r['action']==='error'?'danger':($r['action']==='update'?'warning':''); ?>">
                <td><?php echo (int)$r['row']; ?></td>
                <td><strong><?php echo strtoupper($r['action']); ?></strong><?php if (!empty($r['product_id'])) { ?><br><small>ID <?php echo (int)$r['product_id']; ?></small><?php } ?></td>
                <td><?php echo htmlspecialchars($r['model'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($r['categories'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><code><?php echo htmlspecialchars($r['keyword'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                <td><?php echo htmlspecialchars($r['price'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo $r['status'] ? $text_active : $text_passive; ?></td>
                <td>
                  <?php foreach ($r['errors'] as $e) { ?><div class="text-danger"><i class="fa fa-times-circle"></i> <?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></div><?php } ?>
                  <?php foreach ($r['warnings'] as $w) { ?><div class="text-warning"><i class="fa fa-exclamation-triangle"></i> <?php echo htmlspecialchars($w, ENT_QUOTES, 'UTF-8'); ?></div><?php } ?>
                  <?php if (empty($r['errors']) && empty($r['warnings'])) { ?><span class="text-success"><i class="fa fa-check"></i> Uygun</span><?php } ?>
                </td>
              </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>

        <div class="alert alert-warning"><i class="fa fa-shield"></i> <?php echo $text_apply_warning; ?> <?php if ((int)$summary['errors'] > 0) { ?><strong>Şu anda aktarım kapalı: hata bulunan satırlar var.</strong><?php } ?></div>
        <?php if ((int)$summary['errors'] === 0) { ?>
        <form action="<?php echo $action; ?>" method="post" class="form-inline" onsubmit="return confirm('Önizlemedeki değişiklikler veritabanına uygulanacak. Devam edilsin mi?');">
          <input type="hidden" name="mode" value="apply">
          <input type="hidden" name="import_key" value="<?php echo htmlspecialchars($import_key, ENT_QUOTES, 'UTF-8'); ?>">
          <?php if ($allow_update) { ?><input type="hidden" name="allow_update" value="1"><?php } ?>
          <label class="checkbox-inline" style="margin-right:15px"><input type="checkbox" name="confirm_apply" value="1" required> Önizlemeyi kontrol ettim ve uygulamayı onaylıyorum.</label>
          <button type="submit" class="btn btn-success"><i class="fa fa-play"></i> <?php echo $button_apply; ?></button>
          <a href="<?php echo $clear_url; ?>" class="btn btn-default"><i class="fa fa-times"></i> <?php echo $button_cancel_preview; ?></a>
        </form>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
  </div>
</div>
<?php echo $footer; ?>
