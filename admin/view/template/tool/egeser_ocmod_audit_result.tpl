<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid"><div class="pull-right"><a href="<?php echo $back; ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Modifikasyonlar</a> <a href="<?php echo $rollback; ?>" onclick="return confirm('Denetim öncesindeki TÜM modifikasyon açık/kapalı durumları geri yüklenecek. Devam?');" class="btn btn-warning"><i class="fa fa-undo"></i> Denetimi Geri Al</a></div><h1><i class="fa fa-check-circle"></i> EGESER OCMOD Denetim Raporu</h1></div></div>
  <div class="container-fluid">
    <div class="alert alert-success"><strong>Denetim tamamlandı.</strong> Hiçbir modifikasyon silinmedi. Otomatik kapatma yalnız etkisizliği/bozukluğu kanıtlanan aktif kayıtlar için uygulandı.</div>
    <div class="row">
      <div class="col-sm-2"><div class="well text-center"><strong style="font-size:24px"><?php echo (int)$counts['disabled']; ?></strong><br>Kapatıldı</div></div>
      <div class="col-sm-2"><div class="well text-center"><strong style="font-size:24px"><?php echo (int)$counts['kept']; ?></strong><br>Açık bırakıldı</div></div>
      <div class="col-sm-2"><div class="well text-center"><strong style="font-size:24px"><?php echo (int)$counts['effective']; ?></strong><br>Etkili</div></div>
      <div class="col-sm-2"><div class="well text-center"><strong style="font-size:24px"><?php echo (int)$counts['inert']; ?></strong><br>Etkisiz/Redundant</div></div>
      <div class="col-sm-2"><div class="well text-center"><strong style="font-size:24px"><?php echo (int)$counts['broken']; ?></strong><br>Bozuk</div></div>
      <div class="col-sm-2"><div class="well text-center"><strong style="font-size:24px"><?php echo count($audit['queue']); ?></strong><br>Aktif incelendi</div></div>
    </div>

    <div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Aktif Modifikasyon Sonuçları</h3></div><div class="table-responsive"><table class="table table-bordered table-hover"><thead><tr><th>Modifikasyon</th><th>Karar</th><th>Gerekçe</th><th>OCMOD Cache Farkı</th><th>Frontend Farkı</th></tr></thead><tbody>
    <?php foreach ($audit['results'] as $r) { $d=$r['decision']; $cls = (strpos($d,'disabled')!==false)?'danger':((strpos($d,'kept')!==false)?'success':'default'); ?>
      <tr><td><strong><?php echo htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8'); ?></strong><br><small><?php echo isset($r['version']) ? htmlspecialchars($r['version'], ENT_QUOTES, 'UTF-8') : ''; ?></small></td>
      <td><span class="label label-<?php echo $cls; ?>"><?php echo htmlspecialchars($d, ENT_QUOTES, 'UTF-8'); ?></span></td>
      <td><?php echo htmlspecialchars(isset($r['reason'])?$r['reason']:'', ENT_QUOTES, 'UTF-8'); ?></td>
      <td><?php if (isset($r['tree_diff'])) { echo (int)$r['tree_diff']['changed_count'] . ' dosya'; if (!empty($r['tree_diff']['changed_files'])) { echo '<details><summary>Göster</summary><code>'.htmlspecialchars(implode("\n",$r['tree_diff']['changed_files']),ENT_QUOTES,'UTF-8').'</code></details>'; } } elseif (isset($r['log_effect'])) { echo 'Log LINE: '.(int)$r['log_effect']['applied_lines'].' / NOT FOUND: '.(int)$r['log_effect']['not_found']; } else { echo '-'; } ?></td>
      <td><?php if (isset($r['page_summary'])) { echo 'Değişen: '.(int)$r['page_summary']['changed'].' / Kritik: '.(int)$r['page_summary']['critical']; } else { echo '-'; } ?></td></tr>
    <?php } ?>
    </tbody></table></div></div>

    <div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Başlangıçta Zaten Kapalı Olan Modifikasyonlar — Statik Denetim</h3></div><div class="table-responsive"><table class="table table-bordered"><thead><tr><th>Ad</th><th>XML</th><th>Regex</th><th>Operasyon</th><th>Hedefler</th></tr></thead><tbody>
    <?php foreach ($audit['static'] as $s) { if ((int)$s['original_status']===1) continue; $a=$s['analysis']; ?>
      <tr><td><?php echo htmlspecialchars($s['name'],ENT_QUOTES,'UTF-8'); ?></td><td><?php echo $a['valid_xml']?'Geçerli':'HATALI'; ?></td><td><?php echo $a['regex_valid']?'Geçerli':'HATALI'; ?></td><td><?php echo (int)$a['operations']; ?></td><td><small><?php echo htmlspecialchars(implode(', ',$a['paths']),ENT_QUOTES,'UTF-8'); ?></small></td></tr>
    <?php } ?>
    </tbody></table></div></div>
  </div>
</div>
<?php echo $footer; ?>
