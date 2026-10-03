<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <style type="text/css">
  .eg-fo-badge { display: inline-block; padding: 3px 9px; border-radius: 10px; font-size: 11px; font-weight: 700; color: #fff; }
  .eg-fo-badge--ready { background: #5cb85c; }
  .eg-fo-badge--already-ok { background: #777; }
  .eg-fo-badge--blocked { background: #f0ad4e; }
  .eg-fo-badge--conflict { background: #d9534f; }
  .eg-fo-row--disabled { color: #999; }
  .eg-fo-arrow { color: #999; margin: 0 6px; }
  </style>
  <div class="page-header">
    <div class="container-fluid">
      <h1><i class="fa fa-folder-open-o"></i> Prefabrik Ev Görsellerini Düzenle</h1>
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
      Bu araç <strong>Tek Katlı</strong> ve <strong>Çift Katlı Prefabrik Evler</strong> ürünlerinin görsellerini, mevcut fotoğrafları silmeden/yeniden yüklemeden, doğrudan kendi kategori klasörüne <strong>taşır</strong>. Henüz gerçek fotoğrafı olmayıp ortak bir yer tutucu kullanan ürünlere dokunmaz — onlara önce gerçek görsel yüklemeniz gerekir.
    </div>

    <div id="eg-alert-area"></div>

    <?php if (!$items) { ?>
    <div class="alert alert-info">Görseli olan ürün bulunamadı.</div>
    <?php } else { ?>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> Tek Katlı ve Çift Katlı Prefabrik Evler (<?php echo count($items); ?> ürün, <?php echo $ready_count; ?> tanesi uygun)</h3>
      </div>
      <div class="panel-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <td style="width: 1px;" class="text-center"><input type="checkbox" id="eg-check-all" /></td>
                <td>Ürün</td>
                <td>Kategori</td>
                <td>Durum</td>
                <td>Görsel</td>
                <td>Not</td>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item) { ?>
              <tr class="<?php echo $item['status'] !== 'ready' ? 'eg-fo-row--disabled' : ''; ?>">
                <td class="text-center">
                  <?php if ($item['status'] === 'ready') { ?>
                  <input type="checkbox" class="eg-fo-check" value="<?php echo (int)$item['product_id']; ?>" checked="checked" />
                  <?php } ?>
                </td>
                <td><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($item['category_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                  <?php if ($item['status'] === 'ready') { ?><span class="eg-fo-badge eg-fo-badge--ready">Uygun</span>
                  <?php } elseif ($item['status'] === 'already-ok') { ?><span class="eg-fo-badge eg-fo-badge--already-ok">Zaten düzenli</span>
                  <?php } elseif ($item['status'] === 'blocked') { ?><span class="eg-fo-badge eg-fo-badge--blocked">Yer tutucu</span>
                  <?php } else { ?><span class="eg-fo-badge eg-fo-badge--conflict">Çakışma</span>
                  <?php } ?>
                </td>
                <td><?php echo (int)$item['image_count']; ?><?php if ($item['blocked_count']) { ?> <small>(<?php echo (int)$item['blocked_count']; ?> yer tutucu)</small><?php } ?></td>
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
        <p>En son <?php echo count($latest_log['entries']); ?> ürünün görselleri taşındı. Sorun fark ederseniz geri alabilirsiniz.</p>
        <ul>
          <?php foreach ($latest_log['entries'] as $entry) { ?>
          <li><?php echo htmlspecialchars($entry['product_name'], ENT_QUOTES, 'UTF-8'); ?> — düzenlendi (<?php echo isset($entry['moved']) ? count($entry['moved']) : 0; ?> görsel)</li>
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
    alert('Hiçbir ürün seçilmedi.');
    return;
  }

  if (!confirm(items.length + ' ürünün görselleri tek klasörde toplanacak ve ilgili görsel kayıtları güncellenecek. Devam edilsin mi?')) {
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
      if (r.success) { ok++; } else { fail++; failLines += '<li>' + (r.product_name || r.product_id || '') + ': ' + (r.error || 'bilinmeyen hata') + '</li>'; }
    });
    if (ok) { html += '<div class="alert alert-success">' + ok + ' ürün başarıyla ' + verb + '.</div>'; }
    if (fail) { html += '<div class="alert alert-danger">' + fail + ' üründe sorun oluştu:<ul>' + failLines + '</ul></div>'; }
  }

  $('#eg-alert-area').html(html);
  $('html, body').animate({ scrollTop: 0 }, 200);
}
//--></script>
<?php echo $footer; ?>
