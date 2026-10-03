<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <style type="text/css">
  .eg-sortable { list-style: none; margin: 0; padding: 0; }
  .eg-sortable li { display: flex; align-items: center; gap: 10px; padding: 10px 14px; margin-bottom: 6px; background: #fff; border: 1px solid #ddd; border-radius: 4px; cursor: move; user-select: none; }
  .eg-sortable li:hover { border-color: #aaa; }
  .eg-sortable li.eg-dragging { opacity: 0.4; }
  .eg-sortable li.eg-dragover { border-top: 2px solid #3498db; }
  .eg-sortable li i.fa-bars { color: #999; }
  .eg-bucket-panel { margin-left: 18px; margin-bottom: 18px; }
  </style>
  <div class="page-header">
    <div class="container-fluid">
      <h1><i class="fa fa-sort"></i> Teknik Özellik Sırası</h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div id="eg-alert-area"></div>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> Ürün sayfasındaki "Teknik Özellikler" bölümünde kutular hangi sırada görünsün?</h3>
      </div>
      <div class="panel-body">
        <p class="text-muted">Satırları sürükleyip bırakarak sırayı değiştirin.</p>
        <ul id="eg-bucket-list" class="eg-sortable">
          <?php foreach ($buckets as $bucket) { ?>
          <li draggable="true" data-bucket="<?php echo htmlspecialchars($bucket['name'], ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-bars"></i> <strong><?php echo htmlspecialchars($bucket['name'], ENT_QUOTES, 'UTF-8'); ?></strong></li>
          <?php } ?>
        </ul>
      </div>
    </div>

    <?php foreach ($buckets as $bucket) { ?>
    <div class="panel panel-default eg-bucket-panel">
      <div class="panel-heading">
        <h3 class="panel-title"><?php echo htmlspecialchars($bucket['name'], ENT_QUOTES, 'UTF-8'); ?> kutusu içinde satır sırası</h3>
      </div>
      <div class="panel-body">
        <ul class="eg-sortable" data-bucket-rows="<?php echo htmlspecialchars($bucket['name'], ENT_QUOTES, 'UTF-8'); ?>">
          <?php foreach ($bucket['rows'] as $row) { ?>
          <li draggable="true" data-row="<?php echo htmlspecialchars($row, ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-bars"></i> <?php echo htmlspecialchars($row, ENT_QUOTES, 'UTF-8'); ?></li>
          <?php } ?>
        </ul>
      </div>
    </div>
    <?php } ?>

    <div class="well text-right">
      <button type="button" id="button-save" class="btn btn-primary"><i class="fa fa-save"></i> Kaydet</button>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
function egMakeSortable(list) {
  var dragEl = null;

  list.querySelectorAll('li').forEach(function(li) {
    li.addEventListener('dragstart', function() {
      dragEl = li;
      li.classList.add('eg-dragging');
    });
    li.addEventListener('dragend', function() {
      li.classList.remove('eg-dragging');
      list.querySelectorAll('li').forEach(function(x) { x.classList.remove('eg-dragover'); });
    });
    li.addEventListener('dragover', function(e) {
      e.preventDefault();
      if (li !== dragEl) { li.classList.add('eg-dragover'); }
    });
    li.addEventListener('dragleave', function() {
      li.classList.remove('eg-dragover');
    });
    li.addEventListener('drop', function(e) {
      e.preventDefault();
      li.classList.remove('eg-dragover');
      if (!dragEl || li === dragEl) { return; }
      var items = Array.prototype.slice.call(list.querySelectorAll('li'));
      var dragIndex = items.indexOf(dragEl);
      var dropIndex = items.indexOf(li);
      if (dragIndex < dropIndex) {
        li.parentNode.insertBefore(dragEl, li.nextSibling);
      } else {
        li.parentNode.insertBefore(dragEl, li);
      }
    });
  });
}

document.querySelectorAll('.eg-sortable').forEach(function(list) { egMakeSortable(list); });

$('#button-save').on('click', function() {
  var data = { buckets: [], rows: {} };

  document.querySelectorAll('#eg-bucket-list li').forEach(function(li) {
    data.buckets.push(li.getAttribute('data-bucket'));
  });

  document.querySelectorAll('[data-bucket-rows]').forEach(function(list) {
    var bucket = list.getAttribute('data-bucket-rows');
    data.rows[bucket] = [];
    list.querySelectorAll('li').forEach(function(li) {
      data.rows[bucket].push(li.getAttribute('data-row'));
    });
  });

  $.ajax({
    url: '<?php echo $action; ?>',
    type: 'post',
    data: data,
    dataType: 'json',
    beforeSend: function() {
      $('#button-save').prop('disabled', true);
    },
    complete: function() {
      $('#button-save').prop('disabled', false);
    },
    success: function(json) {
      $('#eg-alert-area').empty();
      if (json.success) {
        $('#eg-alert-area').html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> Sıralama kaydedildi.<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
      } else {
        $('#eg-alert-area').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + (json.error || 'Kaydedilemedi.') + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
      }
    },
    error: function() {
      $('#eg-alert-area').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Sunucuya ulaşılamadı.<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
    }
  });
});
//--></script>
<?php echo $footer; ?>
