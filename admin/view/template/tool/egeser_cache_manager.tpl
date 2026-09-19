<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $refresh_url; ?>" class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_refresh; ?>"><i class="fa fa-refresh"></i></a></div>
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
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-shield"></i> <?php echo $text_dashboard; ?></h3></div>
      <div class="panel-body">
        <p><?php echo $text_intro; ?></p>
        <div class="alert alert-success"><i class="fa fa-lock"></i> <?php echo $text_safety; ?></div>
        <?php if (!$journal_detected) { ?><div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $text_no_journal; ?></div><?php } ?>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <div class="panel panel-primary">
          <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-database"></i> OpenCart Cache</h3></div>
          <div class="panel-body">
            <h2 style="margin-top:0"><?php echo $cache['size']; ?></h2>
            <p><strong>Cache dosyası:</strong> <?php echo (int)$cache['files']; ?></p>
            <p><strong>Yazılabilir:</strong> <?php echo $cache['writable'] ? '<span class="label label-success">Evet</span>' : '<span class="label label-danger">Hayır</span>'; ?></p>
            <p class="text-muted" style="word-break:break-all"><small><?php echo htmlspecialchars($cache['path'], ENT_QUOTES, 'UTF-8'); ?></small></p>
            <a href="<?php echo $clear_cache_url; ?>" class="btn btn-primary" onclick="return confirm('Yalnız cache.* dosyaları temizlenecek. Devam edilsin mi?');"><i class="fa fa-trash"></i> <?php echo $button_clear_cache; ?></a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="panel panel-warning">
          <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-puzzle-piece"></i> OCMOD Modification</h3></div>
          <div class="panel-body">
            <h2 style="margin-top:0"><?php echo $modification['size']; ?></h2>
            <p><strong>Üretilmiş dosya:</strong> <?php echo (int)$modification['files']; ?></p>
            <p class="text-muted" style="word-break:break-all"><small><?php echo htmlspecialchars($modification['path'], ENT_QUOTES, 'UTF-8'); ?></small></p>
            <p class="small"><?php echo $text_ocmod_note; ?></p>
            <a href="<?php echo $ocmod_refresh_url; ?>" class="btn btn-warning" onclick="return confirm('OpenCart çekirdek OCMOD yenileme işlemi çalıştırılacak. Devam edilsin mi?');"><i class="fa fa-refresh"></i> <?php echo $button_refresh_ocmod; ?></a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="panel panel-info">
          <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-microchip"></i> PHP OPcache</h3></div>
          <div class="panel-body">
            <?php if ($opcache['available']) { ?>
              <h2 style="margin-top:0"><?php echo $opcache['enabled'] ? 'Aktif' : 'Pasif'; ?></h2>
              <p><strong>Kullanılan bellek:</strong> <?php echo $opcache['memory'] ? $opcache['memory'] : '-'; ?></p>
              <p><strong>Cache script:</strong> <?php echo (int)$opcache['scripts']; ?></p>
              <a href="<?php echo $reset_opcache_url; ?>" class="btn btn-info" onclick="return confirm('PHP OPcache sıfırlansın mı? Sayfalar ilk istekte tekrar derlenecektir.');"><i class="fa fa-bolt"></i> <?php echo $button_reset_opcache; ?></a>
            <?php } else { ?>
              <h2 style="margin-top:0">Kullanılamıyor</h2>
              <p class="text-muted">Sunucunun web PHP sürecinde OPcache bilgisi alınamadı. Bu bir hata değildir.</p>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>

    <?php if (!empty($cache['other_files'])) { ?>
    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-file-o"></i> Cache Klasöründe Korunan Diğer Dosyalar</h3></div>
      <div class="panel-body">
        <p>Aşağıdaki dosyalar <strong>cache.*</strong> olmadığı için özellikle silinmez:</p>
        <div class="table-responsive"><table class="table table-bordered table-hover"><thead><tr><th>Dosya</th><th>Boyut</th></tr></thead><tbody>
        <?php foreach ($cache['other_files'] as $file) { ?><tr><td><?php echo htmlspecialchars($file['name'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo $file['size']; ?></td></tr><?php } ?>
        </tbody></table></div>
      </div>
    </div>
    <?php } ?>

    <div class="panel panel-danger">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-magic"></i> Güvenli Toplu Temizlik</h3></div>
      <div class="panel-body">
        <p>OpenCart <strong>cache.*</strong> dosyalarını temizler ve PHP OPcache destekleniyorsa sıfırlar. OCMOD yenileme güvenlik nedeniyle ayrı bırakılmıştır.</p>
        <a href="<?php echo $clear_all_safe_url; ?>" class="btn btn-danger btn-lg" onclick="return confirm('Güvenli cache temizliği çalıştırılsın mı? OCMOD bu işlemde değiştirilmez.');"><i class="fa fa-trash"></i> <?php echo $button_clear_all_safe; ?></a>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-history"></i> Son Cache İşlemleri</h3></div>
      <div class="panel-body">
        <?php if ($logs) { ?><pre style="max-height:280px;overflow:auto;background:#fafafa"><?php echo htmlspecialchars(implode("\n", $logs), ENT_QUOTES, 'UTF-8'); ?></pre><?php } else { ?><p class="text-muted">Henüz işlem kaydı yok.</p><?php } ?>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
