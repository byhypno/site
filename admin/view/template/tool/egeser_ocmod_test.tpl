<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $modification_list; ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Modifikasyonlar</a></div>
      <h1><i class="fa fa-flask"></i> <?php echo $heading_title; ?></h1>
      <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div><?php } ?>
    <div class="alert alert-info"><i class="fa fa-shield"></i> <?php echo $text_intro; ?></div>

    <div class="panel panel-success" id="tam-denetim">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-stethoscope"></i> Tam OCMOD Denetimi + Otomatik Temizlik</h3></div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-8">
            <h3 style="margin-top:0">Tüm aktif modifikasyonları otomatik analiz et</h3>
            <p>Bu denetim her aktif OCMOD için XML/regex kontrolü, hedef dosya analizi, <code>system/storage/modification</code> dosya-hash karşılaştırması ve genişletilmiş frontend fingerprint testi yapar.</p>
            <ul>
              <li><strong>Bozuk XML / regex</strong> olan aktif modlar kapatılır.</li>
              <li><strong>Hiç OCMOD operasyonu olmayan</strong> kayıtlar kapatılır.</li>
              <li>Bir mod kapatıldığında modification-cache içinde <strong>hiçbir dosya değişmiyorsa</strong> mevcut build için etkisiz/redundant kabul edilir ve kapatılır.</li>
              <li>Çalışma kodunda gerçek fark üreten modlar <strong>açık bırakılır</strong>.</li>
              <li>Denetim motorunu/çekirdeği hedefleyen kritik modlar toggle edilmez; temiz OCMOD logu üzerinden analiz edilir.</li>
              <li>Hiçbir kayıt silinmez. Denetim başındaki tüm açık/kapalı durumları tek tuşla geri yüklemek için checkpoint tutulur.</li>
            </ul>
          </div>
          <div class="col-md-4">
            <div class="well">
              <strong>Kontrol kapsamı</strong><br>
              XML • Regex • Hedef dosyalar • OCMOD cache • HTTP durum • Title • Meta description • Canonical • Robots • H1/H2 • Form action/input • Tüm href'ler • Tel • WhatsApp • Mailto • Breadcrumb • JSON-LD Schema • JS/CSS asset • Görsel src • Görünür içerik.
            </div>
          </div>
        </div>
        <form action="<?php echo $audit_start; ?>" method="post" onsubmit="return confirm('Tam OCMOD denetimi başlayacak. Etkisizliği kanıtlanan aktif modifikasyonlar otomatik KAPATILACAK, hiçbir mod silinmeyecek ve geri alma checkpointi tutulacak. Devam edilsin mi?');">
          <label style="display:block;margin-bottom:12px"><input type="checkbox" name="auto_disable" value="1" checked="checked"> Etkisiz/bozuk olduğu kanıtlanan aktif modları otomatik kapat</label>
          <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-play"></i> TAM DENETİMİ BAŞLAT</button>
        </form>
      </div>
    </div>

    <?php if ($selected) { ?>
    <div class="panel panel-primary">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-cube"></i> Tekli Modifikasyon Denetimi</h3></div>
      <div class="panel-body">
        <div class="row"><div class="col-sm-8">
          <h3 style="margin-top:0"><?php echo htmlspecialchars($selected['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
          <p><strong>Kod:</strong> <?php echo htmlspecialchars($selected['code'], ENT_QUOTES, 'UTF-8'); ?> &nbsp; <strong>Sürüm:</strong> <?php echo htmlspecialchars($selected['version'], ENT_QUOTES, 'UTF-8'); ?> &nbsp; <strong>Yazar:</strong> <?php echo htmlspecialchars($selected['author'], ENT_QUOTES, 'UTF-8'); ?></p>
          <p><strong>Mevcut durum:</strong> <?php echo $selected['status'] ? '<span class="label label-success">AÇIK</span>' : '<span class="label label-default">KAPALI</span>'; ?></p>
        </div><div class="col-sm-4 text-right">
          <?php if ($analysis['safe_ab']) { ?><span class="label label-success" style="font-size:13px">OTOMATİK A/B UYGUN</span><?php } else { ?><span class="label label-warning" style="font-size:13px">KORUMALI / STATİK</span><?php } ?>
        </div></div><hr>
        <p><strong>XML:</strong> <?php echo $analysis['valid_xml'] ? 'Geçerli' : 'Hatalı'; ?> &nbsp; <strong>Regex:</strong> <?php echo $analysis['regex_valid'] ? 'Geçerli' : 'Hatalı'; ?> &nbsp; <strong>Operasyon:</strong> <?php echo (int)$analysis['operations']; ?></p>
        <p><strong>Hedefler:</strong></p><ul><?php foreach ($analysis['paths'] as $path) { ?><li><code><?php echo htmlspecialchars($path, ENT_QUOTES, 'UTF-8'); ?></code></li><?php } ?></ul>
        <?php if ($analysis['errors']) { ?><div class="alert alert-warning"><strong>Denetim notları:</strong><ul style="margin-bottom:0"><?php foreach ($analysis['errors'] as $e) { ?><li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li><?php } ?></ul></div><?php } ?>
        <?php if ($analysis['safe_ab']) { ?><form action="<?php echo $start; ?>" method="post" onsubmit="return confirm('Seçili modifikasyon geçici A/B teste alınacak ve otomatik eski durumuna getirilecek. Devam?');"><input type="hidden" name="modification_id" value="<?php echo (int)$selected['modification_id']; ?>"><button type="submit" class="btn btn-primary"><i class="fa fa-flask"></i> Tekli A/B Testi</button></form><?php } ?>
      </div>
    </div>
    <?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-list"></i> Modifikasyonlar</h3></div>
      <div class="table-responsive"><table class="table table-bordered table-hover" style="margin-bottom:0">
        <thead><tr><th>Ad</th><th>Yazar</th><th>Sürüm</th><th>Durum</th><th class="text-right">Tekli Test</th></tr></thead>
        <tbody><?php foreach ($modifications as $mod) { ?><tr>
          <td><?php echo htmlspecialchars($mod['name'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($mod['author'], ENT_QUOTES, 'UTF-8'); ?></td><td><?php echo htmlspecialchars($mod['version'], ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo $mod['status'] ? '<span class="label label-success">Açık</span>' : '<span class="label label-default">Kapalı</span>'; ?></td>
          <td class="text-right"><a class="btn btn-primary btn-sm" href="index.php?route=tool/egeser_ocmod_test&amp;token=<?php echo urlencode($token); ?>&amp;modification_id=<?php echo (int)$mod['modification_id']; ?>"><i class="fa fa-flask"></i> Denetle</a></td>
        </tr><?php } ?></tbody>
      </table></div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
