<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <style type="text/css">
  .eg-im-row { display: flex; align-items: flex-start; gap: 16px; padding: 14px 0; border-bottom: 1px solid #eee; }
  .eg-im-main { position: relative; flex: 0 0 120px; }
  .eg-im-main img { width: 120px; height: 120px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px; background: #fafafa; }
  .eg-im-main-btn { display: block; margin-top: 6px; width: 120px; text-align: center; }
  .eg-im-info { flex: 0 0 220px; }
  .eg-im-info .eg-im-name { font-weight: 600; }
  .eg-im-info .eg-im-model { color: #999; font-size: 12px; }
  .eg-im-additional { flex: 1 1 auto; display: flex; flex-wrap: wrap; gap: 8px; }
  .eg-im-add-tile { position: relative; width: 80px; height: 80px; }
  .eg-im-add-tile img { width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px; }
  .eg-im-remove { position: absolute; top: -6px; right: -6px; width: 20px; height: 20px; line-height: 18px; text-align: center; border-radius: 50%; background: #d9534f; color: #fff; font-size: 12px; cursor: pointer; border: 2px solid #fff; }
  .eg-im-upload-tile { width: 80px; height: 80px; border: 2px dashed #ccc; border-radius: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #999; font-size: 22px; }
  .eg-im-upload-tile:hover { border-color: #aaa; color: #777; }
  .eg-im-status { font-size: 12px; margin-top: 4px; min-height: 16px; }
  .eg-im-status--error { color: #d9534f; }
  .eg-im-status--ok { color: #5cb85c; }
  .eg-im-busy { opacity: 0.4; pointer-events: none; }
  </style>
  <div class="page-header">
    <div class="container-fluid">
      <h1><i class="fa fa-picture-o"></i> Ürün Görsellerini Değiştir</h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="alert alert-warning">
      <i class="fa fa-exclamation-triangle"></i>
      Bir ürünün görseline yeni bir dosya yüklediğinizde, eski görsel <strong>otomatik olarak değiştirilir</strong> (başka bir ürün tarafından kullanılmıyorsa eski dosya silinir). Bu işlem geri alınamaz, dikkatli kullanın.
    </div>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-search"></i> Ürün Ara</h3>
      </div>
      <div class="panel-body">
        <form id="eg-im-filter-form" action="<?php echo $filter_action; ?>" method="get" class="form-inline">
          <input type="hidden" name="route" value="catalog/egeser_image_manager" />
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>" />
          <div class="form-group">
            <input type="text" name="filter_name" value="<?php echo htmlspecialchars($filter_name, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Ürün adı..." />
          </div>
          <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Ara</button>
          <span class="text-muted" style="margin-left: 10px;"><?php echo (int)$product_total; ?> ürün bulundu</span>
        </form>
      </div>
    </div>

    <div class="panel panel-default">
      <div class="panel-body">
        <?php if (!$products) { ?>
        <div class="alert alert-info">Ürün bulunamadı.</div>
        <?php } else { ?>
        <?php foreach ($products as $product) { ?>
        <div class="eg-im-row" data-product-id="<?php echo (int)$product['product_id']; ?>">
          <div class="eg-im-main">
            <img src="<?php echo $product['thumb']; ?>" class="eg-im-main-img" alt="" />
            <a href="javascript:void(0);" class="btn btn-default btn-xs eg-im-main-btn eg-im-pick-main"><i class="fa fa-upload"></i> Ana Görseli Değiştir</a>
            <input type="file" class="eg-im-main-file" accept="image/jpeg,image/png,image/gif" style="display:none;" />
          </div>
          <div class="eg-im-info">
            <div class="eg-im-name"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="eg-im-model"><?php echo htmlspecialchars($product['model'], ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="eg-im-status"></div>
          </div>
          <div class="eg-im-additional">
            <?php foreach ($product['additional'] as $img) { ?>
            <div class="eg-im-add-tile" data-product-image-id="<?php echo (int)$img['product_image_id']; ?>">
              <img src="<?php echo $img['thumb']; ?>" alt="" />
              <div class="eg-im-remove" title="Kaldır">&times;</div>
            </div>
            <?php } ?>
            <div class="eg-im-upload-tile eg-im-pick-additional" title="Yeni görsel ekle"><i class="fa fa-plus"></i></div>
            <input type="file" class="eg-im-additional-file" accept="image/jpeg,image/png,image/gif" style="display:none;" />
          </div>
        </div>
        <?php } ?>
        <?php } ?>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12 text-right"><?php echo $pagination; ?></div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
function egImSetStatus($row, message, isError) {
  var $status = $row.find('.eg-im-status');
  $status.removeClass('eg-im-status--ok eg-im-status--error');
  $status.addClass(isError ? 'eg-im-status--error' : 'eg-im-status--ok');
  $status.text(message);
}

$('.eg-im-pick-main').on('click', function() {
  $(this).siblings('.eg-im-main-file').trigger('click');
});

$('.eg-im-main-file').on('change', function() {
  var file = this.files && this.files[0];
  if (!file) { return; }

  var $row = $(this).closest('.eg-im-row');
  var productId = $row.data('product-id');
  var formData = new FormData();
  formData.append('product_id', productId);
  formData.append('file', file);

  $row.addClass('eg-im-busy');

  $.ajax({
    url: '<?php echo $upload_main_action; ?>',
    type: 'post',
    data: formData,
    contentType: false,
    processData: false,
    dataType: 'json',
    success: function(json) {
      if (json.success) {
        $row.find('.eg-im-main-img').attr('src', json.thumb);
        egImSetStatus($row, 'Ana görsel güncellendi.', false);
      } else {
        egImSetStatus($row, json.error || 'Bir hata oluştu.', true);
      }
    },
    error: function(xhr) {
      egImSetStatus($row, 'Sunucu hatası (HTTP ' + xhr.status + ').', true);
    },
    complete: function() {
      $row.removeClass('eg-im-busy');
    }
  });

  $(this).val('');
});

$('.eg-im-pick-additional').on('click', function() {
  $(this).siblings('.eg-im-additional-file').trigger('click');
});

$('.eg-im-additional-file').on('change', function() {
  var file = this.files && this.files[0];
  if (!file) { return; }

  var $row = $(this).closest('.eg-im-row');
  var productId = $row.data('product-id');
  var formData = new FormData();
  formData.append('product_id', productId);
  formData.append('file', file);

  $row.addClass('eg-im-busy');

  $.ajax({
    url: '<?php echo $upload_additional_action; ?>',
    type: 'post',
    data: formData,
    contentType: false,
    processData: false,
    dataType: 'json',
    success: function(json) {
      if (json.success) {
        var tile = $('<div class="eg-im-add-tile"><img /><div class="eg-im-remove" title="Kaldır">&times;</div></div>');
        tile.attr('data-product-image-id', json.product_image_id);
        tile.find('img').attr('src', json.thumb);
        $row.find('.eg-im-upload-tile').before(tile);
        egImSetStatus($row, 'Görsel eklendi.', false);
      } else {
        egImSetStatus($row, json.error || 'Bir hata oluştu.', true);
      }
    },
    error: function(xhr) {
      egImSetStatus($row, 'Sunucu hatası (HTTP ' + xhr.status + ').', true);
    },
    complete: function() {
      $row.removeClass('eg-im-busy');
    }
  });

  $(this).val('');
});

$(document).on('click', '.eg-im-remove', function() {
  if (!confirm('Bu görseli kaldırmak istediğinize emin misiniz?')) { return; }

  var $tile = $(this).closest('.eg-im-add-tile');
  var $row = $(this).closest('.eg-im-row');
  var productImageId = $tile.data('product-image-id');

  $row.addClass('eg-im-busy');

  $.ajax({
    url: '<?php echo $remove_additional_action; ?>',
    type: 'post',
    data: { product_image_id: productImageId },
    dataType: 'json',
    success: function(json) {
      if (json.success) {
        $tile.remove();
        egImSetStatus($row, 'Görsel kaldırıldı.', false);
      } else {
        egImSetStatus($row, json.error || 'Bir hata oluştu.', true);
      }
    },
    error: function(xhr) {
      egImSetStatus($row, 'Sunucu hatası (HTTP ' + xhr.status + ').', true);
    },
    complete: function() {
      $row.removeClass('eg-im-busy');
    }
  });
});
//--></script>
<?php echo $footer; ?>
