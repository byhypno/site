<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $back; ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Modifikasyonlar</a></div>
      <h1><i class="fa fa-shield"></i> EGESER OCMOD Final Temizlik</h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div><?php } ?>
    <div class="alert alert-info">
      <strong>Güvenlik kuralı:</strong> Bu ekran yalnız <strong>kapalı</strong> ve son Tam Denetimde etkisiz/bozuk olduğu kanıtlanan veya statik analizde <strong>0 operasyon / bozuk XML-regex</strong> olan OCMOD kayıtlarını listeler. Açık/etkili modifikasyonlar silinemez.
    </div>
    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-check-square-o"></i> Silinebilecek Güvenli Adaylar (<?php echo count($plan); ?>)</h3></div>
      <div class="panel-body">
        <?php if ($plan) { ?>
        <form action="<?php echo $action; ?>" method="post" id="egeser-cleanup-form">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead><tr><td class="text-center" style="width:35px"><input type="checkbox" checked onclick="$('.egeser-candidate').prop('checked', this.checked);"></td><td>Modifikasyon</td><td>Kaynak</td><td>Gerekçe</td><td>Yazar</td></tr></thead>
              <tbody>
              <?php foreach ($plan as $row) { ?>
                <tr>
                  <td class="text-center"><input class="egeser-candidate" type="checkbox" name="selected[]" value="<?php echo (int)$row['modification_id']; ?>" checked></td>
                  <td><strong><?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></strong><br><small>ID: <?php echo (int)$row['modification_id']; ?> · v<?php echo htmlspecialchars($row['version'], ENT_QUOTES, 'UTF-8'); ?></small></td>
                  <td><?php echo htmlspecialchars($row['source'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($row['reason'], ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
              <?php } ?>
              </tbody>
            </table>
          </div>
          <div class="alert alert-warning"><i class="fa fa-database"></i> İşlemden önce silinecek kayıtların tam XML dahil birebir yedeği <code>system/storage/logs/</code> altında oluşturulur. Sonuç ekranından tek tuşla geri alınabilir.</div>
          <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Seçili kapalı ve kanıtlı etkisiz OCMOD kayıtları silinecek, ardından cache ve ocmod.log temiz şekilde yeniden oluşturulacak. Devam edilsin mi?');"><i class="fa fa-trash"></i> Seçili Güvenli Adayları Temizle</button>
        </form>
        <?php } else { ?>
          <div class="alert alert-success"><i class="fa fa-check"></i> Silinebilecek kanıtlı etkisiz/bozuk OCMOD kaydı bulunamadı.</div>
        <?php } ?>
      </div>
    </div>

    <?php if ($backups) { ?>
    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-history"></i> Son Temizlik Yedekleri</h3></div>
      <div class="panel-body"><div class="table-responsive"><table class="table table-bordered">
        <thead><tr><td>Tarih</td><td>Silinen Kayıt</td><td>Durum</td><td class="text-right">İşlem</td></tr></thead><tbody>
        <?php foreach ($backups as $b) { ?><tr><td><?php echo htmlspecialchars($b['created_at'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo count(isset($b['rows'])?$b['rows']:array()); ?></td><td><?php echo !empty($b['restored_at']) ? 'Geri alındı' : 'Aktif yedek'; ?></td><td class="text-right"><?php if (empty($b['restored_at'])) { ?><a class="btn btn-warning" href="<?php echo $b['rollback_url']; ?>" onclick="return confirm('Bu temizlik yedeği geri yüklensin mi?');"><i class="fa fa-undo"></i> Geri Al</a><?php } ?></td></tr><?php } ?>
        </tbody>
      </table></div></div>
    </div>
    <?php } ?>
  </div>
</div>
<?php echo $footer; ?>
