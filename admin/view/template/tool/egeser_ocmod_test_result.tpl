<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid"><div class="pull-right"><a href="<?php echo $back; ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Test Paneli</a></div><h1><i class="fa fa-flask"></i> OCMOD A/B Test Sonucu</h1></div></div>
  <div class="container-fluid">
    <?php if (!$result_state['restored']) { ?><div class="alert alert-danger"><strong>UYARI:</strong> Modifikasyonun orijinal durumu doğrulanamadı. <a href="<?php echo $restore; ?>" class="btn btn-danger btn-xs">Acil Geri Yükle</a></div><?php } else { ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> Test tamamlandı ve modifikasyon otomatik olarak orijinal durumuna geri getirildi.</div><?php } ?>
    <div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title"><?php echo htmlspecialchars($result_state['modification_name'], ENT_QUOTES, 'UTF-8'); ?></h3></div><div class="panel-body">
      <p><strong>Test:</strong> <?php echo $result_state['original_status'] ? 'AÇIK → KAPALI → AÇIK' : 'KAPALI → AÇIK → KAPALI'; ?></p>
      <p><strong>Başlangıç:</strong> <?php echo htmlspecialchars($result_state['started_at'], ENT_QUOTES, 'UTF-8'); ?></p>
    </div></div>

    <div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Sayfa Karşılaştırmaları</h3></div><div class="table-responsive"><table class="table table-bordered table-hover" style="margin-bottom:0">
      <thead><tr><th>Sayfa</th><th>Sonuç</th><th>HTTP</th><th>Title</th><th>H1</th><th>Form</th><th>Telefon</th><th>WhatsApp</th><th>Farklar</th></tr></thead>
      <tbody><?php foreach ($result_state['comparison'] as $r) { ?>
        <tr class="<?php echo $r['severity']==='critical'?'danger':($r['severity']==='changed'?'warning':'success'); ?>">
          <td><strong><?php echo htmlspecialchars($r['label'], ENT_QUOTES, 'UTF-8'); ?></strong><br><small><?php echo htmlspecialchars($r['url'], ENT_QUOTES, 'UTF-8'); ?></small></td>
          <td><?php if ($r['severity']==='same') { ?><span class="label label-success">DEĞİŞMEDİ</span><?php } elseif ($r['severity']==='changed') { ?><span class="label label-warning">DEĞİŞTİ</span><?php } else { ?><span class="label label-danger">KRİTİK FARK</span><?php } ?></td>
          <td><?php echo (int)$r['before']['http_code']; ?> → <?php echo (int)$r['after']['http_code']; ?></td>
          <td><?php echo htmlspecialchars($r['before']['title'], ENT_QUOTES, 'UTF-8'); ?><br><strong>→</strong> <?php echo htmlspecialchars($r['after']['title'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo (int)$r['before']['h1_count']; ?> → <?php echo (int)$r['after']['h1_count']; ?><br><small><?php echo htmlspecialchars($r['before']['h1_text'], ENT_QUOTES, 'UTF-8'); ?> → <?php echo htmlspecialchars($r['after']['h1_text'], ENT_QUOTES, 'UTF-8'); ?></small></td>
          <td><?php echo (int)$r['before']['form_count']; ?> → <?php echo (int)$r['after']['form_count']; ?></td>
          <td><?php echo (int)$r['before']['tel_count']; ?> → <?php echo (int)$r['after']['tel_count']; ?></td>
          <td><?php echo (int)$r['before']['whatsapp_count']; ?> → <?php echo (int)$r['after']['whatsapp_count']; ?></td>
          <td><?php echo $r['diffs'] ? htmlspecialchars(implode(', ', $r['diffs']), ENT_QUOTES, 'UTF-8') : '-'; ?></td>
        </tr>
      <?php } ?></tbody>
    </table></div></div>
    <div class="alert alert-info"><strong>Yorum:</strong> “Değişmedi”, yalnızca bu panelin test ettiği sayfalarda görünür/ölçülebilir fark bulunmadığı anlamına gelir. Bir modifikasyonu silme kararı, hedef dosyaları ve admin/system etkileri de değerlendirilerek verilmelidir.</div>
  </div>
</div>
<?php echo $footer; ?>
