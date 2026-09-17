<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid"><div class="pull-right"><a href="<?php echo $template_url; ?>" class="btn btn-default"><i class="fa fa-download"></i> CSV Şablonu</a></div><h1>EGESER 301 Yönlendirme Merkezi</h1><ul class="breadcrumb"><?php foreach($breadcrumbs as $b){ ?><li><a href="<?php echo $b['href']; ?>"><?php echo $b['text']; ?></a></li><?php } ?></ul></div></div>
  <div class="container-fluid">
    <?php if($error_warning){ ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_warning,ENT_QUOTES,'UTF-8'); ?></div><?php } ?>
    <?php if($success){ ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($success,ENT_QUOTES,'UTF-8'); ?></div><?php } ?>
    <div class="alert alert-info"><strong>Güvenlik:</strong> Yalnızca site içi hedeflere izin verilir. Pasif + "blokla" kuralı eski URL'nin yeni sistemde yanlış bir sayfaya düşmesini engelleyip 404 verir. Ürün yönlendirmelerini hedef ürün 200 OK olmadan aktif etmeyin.</div>

    <div class="row">
      <div class="col-md-6"><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title"><i class="fa fa-plus"></i> Tek Kural Ekle</h3></div><div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" class="form-horizontal">
          <input type="hidden" name="mode" value="add">
          <div class="form-group"><label class="col-sm-3 control-label">Eski URL</label><div class="col-sm-9"><input name="source_url" class="form-control" placeholder="/eski-url veya /index.php?route=..."></div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Yeni URL</label><div class="col-sm-9"><input name="target_url" class="form-control" placeholder="/yeni-url"></div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Öncelik</label><div class="col-sm-9"><select name="priority" class="form-control"><option>KRITIK</option><option>YUKSEK</option><option selected>NORMAL</option><option>DUSUK</option></select></div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Kaynak</label><div class="col-sm-9"><input name="source" value="Manuel" class="form-control"></div></div>
          <div class="form-group"><label class="col-sm-3 control-label">Not</label><div class="col-sm-9"><input name="note" class="form-control"></div></div>
          <div class="form-group"><div class="col-sm-offset-3 col-sm-9"><label class="checkbox-inline"><input type="checkbox" name="status" value="1"> Aktif 301</label> &nbsp; <label class="checkbox-inline"><input type="checkbox" name="block_disabled" value="1" checked> Pasifken 404 blokla</label></div></div>
          <div class="form-group"><div class="col-sm-offset-3 col-sm-9"><button class="btn btn-primary"><i class="fa fa-save"></i> Kaydet</button></div></div>
        </form>
      </div></div></div>

      <div class="col-md-6"><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title"><i class="fa fa-upload"></i> Toplu CSV – Dry Run</h3></div><div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
          <input type="hidden" name="mode" value="preview_csv"><div class="form-group"><label class="col-sm-3 control-label">CSV</label><div class="col-sm-9"><input type="file" name="import_file" accept=".csv" required class="form-control"></div></div>
          <div class="form-group"><div class="col-sm-offset-3 col-sm-9"><button class="btn btn-primary"><i class="fa fa-search"></i> Önizle / Dry Run</button></div></div>
        </form>
        <p class="help-block">Sütunlar: source_url; target_url; status; block_disabled; priority; source; note</p>
      </div></div></div>
    </div>

    <?php if(!empty($preview_summary)){ ?>
    <div class="panel panel-warning"><div class="panel-heading"><h3 class="panel-title">CSV Önizleme</h3></div><div class="panel-body">
      <p><strong><?php echo (int)$preview_summary['rows']; ?></strong> satır · Yeni <?php echo (int)$preview_summary['creates']; ?> · Güncelleme <?php echo (int)$preview_summary['updates']; ?> · Hata <?php echo (int)$preview_summary['errors']; ?> · Uyarı <?php echo (int)$preview_summary['warnings']; ?></p>
      <div class="table-responsive"><table class="table table-bordered table-condensed"><thead><tr><th>Satır</th><th>İşlem</th><th>Eski</th><th>Yeni</th><th>Durum</th><th>Kontrol</th></tr></thead><tbody>
      <?php foreach($preview as $r){ ?><tr class="<?php echo !empty($r['errors'])?'danger':''; ?>"><td><?php echo (int)$r['line']; ?></td><td><?php echo strtoupper($r['action']); ?></td><td><code><?php echo htmlspecialchars($r['source_url'],ENT_QUOTES,'UTF-8'); ?></code></td><td><code><?php echo htmlspecialchars($r['target_url'],ENT_QUOTES,'UTF-8'); ?></code></td><td><?php echo $r['status']?'AKTİF':'PASİF'; ?></td><td><?php foreach($r['errors'] as $e){ ?><div class="text-danger"><?php echo htmlspecialchars($e,ENT_QUOTES,'UTF-8'); ?></div><?php } foreach($r['warnings'] as $w){ ?><div class="text-warning"><?php echo htmlspecialchars($w,ENT_QUOTES,'UTF-8'); ?></div><?php } ?></td></tr><?php } ?>
      </tbody></table></div>
      <?php if((int)$preview_summary['errors']===0){ ?><form action="<?php echo $action; ?>" method="post" onsubmit="return confirm('Dry Run listesindeki kurallar veritabanına uygulanacak. Devam?');"><input type="hidden" name="mode" value="apply_csv"><label><input type="checkbox" name="confirm_apply" value="1" required> Önizlemeyi kontrol ettim.</label> <button class="btn btn-success"><i class="fa fa-play"></i> CSV'yi Uygula</button></form><?php } ?>
    </div></div><?php } ?>

    <div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title"><i class="fa fa-random"></i> Kayıtlı Kurallar (<?php echo count($rules); ?>)</h3></div><div class="panel-body"><div class="table-responsive">
      <table class="table table-bordered table-hover table-condensed"><thead><tr><th>ID</th><th>Öncelik</th><th>Eski URL</th><th>Hedef</th><th>Durum</th><th>Blok</th><th>Kaynak / Not</th><th>Hit</th><th>İşlem</th></tr></thead><tbody>
      <?php foreach($rules as $r){ ?><tr class="<?php echo $r['status']?'success':($r['block_disabled']?'warning':''); ?>"><td><?php echo (int)$r['redirect_id']; ?></td><td><?php echo htmlspecialchars($r['priority'],ENT_QUOTES,'UTF-8'); ?></td><td><code><?php echo htmlspecialchars($r['source_url'],ENT_QUOTES,'UTF-8'); ?></code></td><td><a href="<?php echo htmlspecialchars($catalog_base.$r['target_url'],ENT_QUOTES,'UTF-8'); ?>" target="_blank"><code><?php echo htmlspecialchars($r['target_url'],ENT_QUOTES,'UTF-8'); ?></code></a></td><td><strong><?php echo $r['status']?'AKTİF 301':'PASİF'; ?></strong></td><td><?php echo $r['block_disabled']?'404':'Geç'; ?></td><td><?php echo htmlspecialchars($r['source'],ENT_QUOTES,'UTF-8'); ?><br><small><?php echo htmlspecialchars($r['note'],ENT_QUOTES,'UTF-8'); ?></small></td><td><?php echo (int)$r['hits']; ?><?php if(!empty($r['last_hit'])){ ?><br><small><?php echo htmlspecialchars($r['last_hit'],ENT_QUOTES,'UTF-8'); ?></small><?php } ?></td><td style="white-space:nowrap"><form action="<?php echo $action; ?>" method="post" style="display:inline"><input type="hidden" name="mode" value="toggle"><input type="hidden" name="redirect_id" value="<?php echo (int)$r['redirect_id']; ?>"><button class="btn btn-xs <?php echo $r['status']?'btn-warning':'btn-success'; ?>" onclick="return confirm('Kural durumu değiştirilsin mi?');"><i class="fa fa-power-off"></i></button></form> <form action="<?php echo $action; ?>" method="post" style="display:inline"><input type="hidden" name="mode" value="delete"><input type="hidden" name="redirect_id" value="<?php echo (int)$r['redirect_id']; ?>"><button class="btn btn-xs btn-danger" onclick="return confirm('Kural silinsin mi?');"><i class="fa fa-trash"></i></button></form></td></tr><?php } ?>
      <?php if(!$rules){ ?><tr><td colspan="9" class="text-center">Kural yok.</td></tr><?php } ?>
      </tbody></table>
    </div></div></div>
  </div>
</div>
<?php echo $footer; ?>
