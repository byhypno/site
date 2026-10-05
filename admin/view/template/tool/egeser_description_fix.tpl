<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if (!empty($error_warning)) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php } ?>
    <?php if ($success) { ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php } ?>

    <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $text_intro; ?></div>

    <?php if (empty($affected)) { ?>
    <div class="panel panel-default">
      <div class="panel-body text-center text-muted"><?php echo $text_empty; ?></div>
    </div>
    <?php } else { ?>
    <form action="<?php echo $action; ?>" method="post" onsubmit="return confirm('Seçilen ürünlerin açıklamaları güncellenecek. Devam edilsin mi?');">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><i class="fa fa-wrench"></i> <?php echo count($affected); ?> ürün etkileniyor</h3>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th style="width:30px;"><input type="checkbox" id="check-all" checked></th>
                <th style="width:220px;"><?php echo $column_product; ?></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($affected as $r) { ?>
              <tr>
                <td><input type="checkbox" name="product_id[]" value="<?php echo (int)$r['product_id']; ?>" class="row-check" checked></td>
                <td><strong><?php echo htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8'); ?></strong><br><small class="text-muted">ID <?php echo (int)$r['product_id']; ?></small></td>
                <td>
                  <a href="#desc-<?php echo (int)$r['product_id']; ?>" data-toggle="collapse"><i class="fa fa-eye"></i> Karşılaştır (önce/sonra)</a>
                  <div id="desc-<?php echo (int)$r['product_id']; ?>" class="collapse" style="margin-top:8px;">
                    <label class="text-danger"><?php echo $text_old; ?></label>
                    <div style="max-height:150px;overflow:auto;white-space:pre-wrap;background:#fff5f5;border:1px solid #f5c6cb;padding:8px;font-size:12px;margin-bottom:8px;"><?php echo htmlspecialchars($r['description'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <label class="text-success"><?php echo $text_new; ?></label>
                    <div style="max-height:150px;overflow:auto;white-space:pre-wrap;background:#f3fbf4;border:1px solid #c3e6cb;padding:8px;font-size:12px;"><?php echo htmlspecialchars($r['fixed'], ENT_QUOTES, 'UTF-8'); ?></div>
                  </div>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
        <div class="panel-body">
          <label class="checkbox-inline" style="margin-right:15px"><input type="checkbox" name="confirm_apply" value="1" required> Yukarıdaki değişiklikleri kontrol ettim, onaylıyorum.</label>
          <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> <?php echo $button_apply; ?></button>
        </div>
      </div>
    </form>
    <?php } ?>
  </div>
</div>
<script>
document.getElementById('check-all') && document.getElementById('check-all').addEventListener('change', function () {
  var checked = this.checked;
  document.querySelectorAll('.row-check').forEach(function (c) { c.checked = checked; });
});
</script>
<?php echo $footer; ?>
