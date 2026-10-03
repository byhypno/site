<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <style type="text/css">
  .eg-fo-badge { display: inline-block; padding: 3px 9px; border-radius: 10px; font-size: 11px; font-weight: 700; color: #fff; }
  .eg-fo-badge--ready { background: #5cb85c; }
  .eg-fo-badge--orphan { background: #999; }
  .eg-fo-badge--shared { background: #f0ad4e; }
  .eg-fo-badge--conflict { background: #d9534f; }
  .eg-fo-row--disabled { color: #999; }
  .eg-fo-arrow { color: #999; margin: 0 6px; }
  .eg-fo-new-name { font-weight: 700; color: #1d7a1d; }
  </style>
  <div class="page-header">
    <div class="container-fluid">
      <h1><i class="fa fa-folder-open-o"></i> Ürün Görsel Klasörlerini Düzenle</h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
    <?php } ?>

    <div class="alert alert-warning">
      <i class="fa fa-exclamation-triangle"></i>
      <strong>Dikkatli kullanın:</strong> Bu araç <code>image/catalog/urunler/</code> altındaki numara isimli klasörleri, o klasörü kullanan ürünün adına göre yeniden adlandırır ve veritabanındaki ilgili ürün görsel kayıtlarını aynı anda günceller. Birden fazla ürünün paylaştığı veya veritabanında eşleşmeyen klasörler otomatik olarak atlanır, dokunulmaz.
    </div>

    <div id="eg-alert-area"></div>

    <?php if (!$base_dir_exists) { ?>
    <div class="alert alert-info">image/catalog/urunler/ klasörü bulunamadı.</div>
    <?php } elseif (!$items) { ?>
    <div class="alert alert-info">Bu klasör altında sayısal isimli bir alt klasör bulunamadı.</div>
    <?php } else { ?>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> Bulunan klasörler (<?php echo count($items); ?>) — uygun olanlar: <?php echo $ready_count; ?></h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <td style="width: 1px;" class="text-center"><input type="checkbox" id="eg-check-all" /></td>
                <td>Klasör</td>
                <td>Durum</td>
                <td>Ürün</td>
                <td>Yeni ad</td>
                <td>Görsel</td>
                <td>Not</td>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item) { ?>
              <tr class="<?php echo $item['status'] !== 'ready' ? 'eg-fo-row--disabled' : ''; ?>">
                <td class="text-center">
                  <?php if ($item['status'] === 'ready') { ?>
                  <input type="checkbox" class="eg-fo-check" value="<?php echo htmlspecialchars($item['folder_id'], ENT_QUOTES, 'UTF-8'); ?>" checked="checked" />
                  <?php } ?>
                </td>
                <td><code><?php echo htmlspecialchars($item['folder_id'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                <td>
                  <?php if ($item['status'] === 'ready') { ?><span class="eg-fo-badge eg-fo-badge--ready">Uygun</span>
                  <?php } elseif ($item['status'] === 'orphan') { ?><span class="eg-fo-badge eg-fo-badge--orphan">Eşleşmedi</span>
                  <?php } elseif ($item['status'] === 'shared') { ?><span class="eg-fo-badge eg-fo-badge--shared">Paylaşılan</span>
                  <?php } else { ?><span class="eg-fo-badge eg-fo-badge--conflict">Çakışma</span>
                  <?php } ?>
                </td>
                <td><?php echo $item['product_name'] !== '' ? htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') : '—'; ?></td>
                <td>
                  <?php if ($item['new_slug'] !== '') { ?>
                  <span class="eg-fo-arrow">→</span><span class="eg-fo-new-name"><?php echo htmlspecialchars($item['new_slug'], ENT_QUOTES, 'UTF-8'); ?></span>
                  <?php } else { ?>—<?php } ?>
                </td>
                <td><?php echo (int)$item['image_count']; ?></td>
                <td><small><?php echo htmlspecialchars($item['note'], ENT_QUOTES, 'UTF-8'); ?></small></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="panel-footer text-right">
        <button type="button" id="button-apply" class="btn btn-primary" <?php echo $ready_count ? '' : 'disabled="disabled"'; ?>><i class="fa fa-check"></i> Seçilenleri Uygula</button>
      </div>
    </div>
    <?php } ?>

    <?php if ($latest_log) { ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-history"></i> Son işlem</h3>
      </div>
      <div class="panel-body">
        <p>En son <?php echo count($latest_log['entries']); ?> klasör yeniden adlandırıldı. Sorun fark ederseniz geri alabilirsiniz.</p>
        <ul>
          <?php foreach ($latest_log['entries'] as $entry) { ?>
          <li><code><?php echo htmlspecialchars($entry['folder_id'], ENT_QUOTES, 'UTF-8'); ?></code> <span class="eg-fo-arrow">→</span> <code><?php echo htmlspecialchars($entry['new_slug'], ENT_QUOTES, 'UTF-8'); ?></code> (<?php echo htmlspecialchars($entry['product_name'], ENT_QUOTES, 'UTF-8'); ?>)</li>
          <?php } ?>
        </ul>
        <button type="button" id="button-revert" class="btn btn-default"><i class="fa fa-undo"></i> Son İşlemi Geri Al</button>
      </div>
    </div>
    <?php } ?>

  </div>
</div>
<script type="text/javascript"><!--
$('#eg-check-all').on('change', function() {
  $('.eg-fo-check').prop('checked', $(this).is(':checked'));
});

$('#button-apply').on('click', function() {
  var items = [];
  $('.eg-fo-check:checked').each(function() { items.push($(this).val()); });

  if (!items.length) {
    alert('Hiçbir klasör seçilmedi.');
    return;
  }

  if (!confirm(items.length + ' klasör yeniden adlandırılacak ve ilgili ürünlerin görsel kayıtları güncellenecek. Devam edilsin mi?')) {
    return;
  }

  $.ajax({
    url: '<?php echo $action; ?>',
    type: 'post',
    data: { items: items },
    dataType: 'json',
    beforeSend: function() { $('#button-apply').prop('disabled', true); },
    complete: function() { $('#button-apply').prop('disabled', false); },
    success: function(json) {
      renderResults(json, 'uygulandı');
      if (json.success) { setTimeout(function() { location.reload(); }, 1500); }
    },
    error: function() {
      $('#eg-alert-area').html('<div class="alert alert-danger">Sunucuya ulaşılamadı.</div>');
    }
  });
});

$('#button-revert').on('click', function() {
  if (!confirm('Son işlemi geri almak istediğinize emin misiniz?')) { return; }

  $.ajax({
    url: '<?php echo $revert_action; ?>',
    type: 'post',
    dataType: 'json',
    beforeSend: function() { $('#button-revert').prop('disabled', true); },
    complete: function() { $('#button-revert').prop('disabled', false); },
    success: function(json) {
      renderResults(json, 'geri alındı');
      if (json.success) { setTimeout(function() { location.reload(); }, 1500); }
    },
    error: function() {
      $('#eg-alert-area').html('<div class="alert alert-danger">Sunucuya ulaşılamadı.</div>');
    }
  });
});

function renderResults(json, verb) {
  var html = '';

  if (!json.success) {
    html += '<div class="alert alert-danger">' + (json.error || 'Bir hata oluştu.') + '</div>';
  } else {
    var ok = 0, fail = 0, failLines = '';
    (json.results || []).forEach(function(r) {
      if (r.success) { ok++; } else { fail++; failLines += '<li>' + r.folder_id + ': ' + (r.error || 'bilinmeyen hata') + '</li>'; }
    });
    if (ok) { html += '<div class="alert alert-success">' + ok + ' klasör başarıyla ' + verb + '.</div>'; }
    if (fail) { html += '<div class="alert alert-danger">' + fail + ' klasörde sorun oluştu:<ul>' + failLines + '</ul></div>'; }
  }

  $('#eg-alert-area').html(html);
  $('html, body').animate({ scrollTop: 0 }, 200);
}
//--></script>
<?php echo $footer; ?>
