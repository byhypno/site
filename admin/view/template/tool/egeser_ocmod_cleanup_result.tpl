<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid">
    <div class="pull-right"><a href="<?php echo $back; ?>" class="btn btn-primary"><i class="fa fa-list"></i> Modifikasyonlar</a> <a href="<?php echo $rollback; ?>" class="btn btn-warning" onclick="return confirm('Silinen OCMOD kayıtları geri yüklensin mi?');"><i class="fa fa-undo"></i> Temizliği Geri Al</a></div>
    <h1><i class="fa fa-check-circle"></i> EGESER OCMOD Final Temizlik Sonucu</h1>
  </div></div>
  <div class="container-fluid">
    <div class="alert alert-success"><strong>Temizlik tamamlandı.</strong> <?php echo count(isset($cleanup['rows'])?$cleanup['rows']:array()); ?> kapalı ve kanıtlı etkisiz/bozuk OCMOD kaydı yedeklenerek silindi; modification cache yeniden oluşturuldu.</div>
    <div class="row">
      <div class="col-sm-2"><div class="well text-center"><h2><?php echo count(isset($cleanup['rows'])?$cleanup['rows']:array()); ?></h2><small>Silinen</small></div></div>
      <div class="col-sm-2"><div class="well text-center"><h2><?php echo count($remaining_active); ?></h2><small>Aktif Kalan</small></div></div>
      <div class="col-sm-2"><div class="well text-center"><h2><?php echo (int)$health['mods']; ?></h2><small>Log MOD</small></div></div>
      <div class="col-sm-2"><div class="well text-center"><h2><?php echo (int)$health['not_found']; ?></h2><small>NOT FOUND</small></div></div>
      <div class="col-sm-2"><div class="well text-center"><h2><?php echo (int)$health['regex_errors']; ?></h2><small>Regex Hatası</small></div></div>
      <div class="col-sm-2"><div class="well text-center"><h2><?php echo (int)$health['fatal']; ?></h2><small>Fatal/Parse</small></div></div>
    </div>

    <?php if ((int)$health['regex_errors'] === 0 && (int)$health['fatal'] === 0) { ?><div class="alert alert-success"><i class="fa fa-check"></i> Yeni OCMOD logunda <strong>No ending delimiter</strong> veya Fatal/Parse hatası görülmedi.</div><?php } else { ?><div class="alert alert-danger"><i class="fa fa-warning"></i> Yeni OCMOD logunda hata izi bulundu. Canlıya geçmeden incelenmeli.</div><?php } ?>

    <div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Silinen Kayıtlar</h3></div><div class="panel-body"><div class="table-responsive"><table class="table table-bordered table-hover">
      <thead><tr><td>ID</td><td>Ad</td><td>Versiyon</td><td>Eski Durum</td></tr></thead><tbody>
      <?php foreach ((array)$cleanup['rows'] as $row) { ?><tr><td><?php echo (int)$row['modification_id']; ?></td><td><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($row['version'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo (int)$row['status'] ? 'Açık' : 'Kapalı'; ?></td></tr><?php } ?>
      </tbody>
    </table></div></div></div>

    <div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Aktif Kalan OCMOD'lar</h3></div><div class="panel-body"><div class="table-responsive"><table class="table table-bordered table-hover">
      <thead><tr><td>ID</td><td>Ad</td><td>Versiyon</td><td>Yazar</td></tr></thead><tbody>
      <?php foreach ($remaining_active as $row) { ?><tr><td><?php echo (int)$row['modification_id']; ?></td><td><strong><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></strong></td><td><?php echo htmlspecialchars($row['version'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'); ?></td></tr><?php } ?>
      </tbody>
    </table></div></div></div>
  </div>
</div>
<?php echo $footer; ?>
