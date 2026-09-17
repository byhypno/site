<?php echo $header; ?><?php echo $column_left; ?>
<div id="content"><div class="page-header"><div class="container-fluid">
<div class="pull-right"><button type="submit" form="form-egeser-theme" class="btn btn-primary"><i class="fa fa-save"></i></button>
<a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
<h1><?php echo $heading_title; ?></h1>
<ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
</div></div><div class="container-fluid">
<?php if ($error_warning) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div><?php } ?>
<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title"><?php echo $text_edit; ?></h3></div><div class="panel-body">
<form action="<?php echo $action; ?>" method="post" id="form-egeser-theme" class="form-horizontal">
<div class="form-group"><label class="col-sm-3 control-label"><?php echo $entry_directory; ?></label><div class="col-sm-9"><input type="text" name="egeser_directory" value="<?php echo htmlspecialchars($egeser_directory, ENT_QUOTES, 'UTF-8'); ?>" class="form-control" readonly></div></div>
<div class="form-group"><label class="col-sm-3 control-label"><?php echo $entry_status; ?></label><div class="col-sm-9"><select name="egeser_status" class="form-control"><option value="1" <?php echo $egeser_status?'selected':''; ?>><?php echo $text_enabled; ?></option><option value="0" <?php echo !$egeser_status?'selected':''; ?>><?php echo $text_disabled; ?></option></select></div></div>
<div class="form-group"><label class="col-sm-3 control-label"><?php echo $entry_product_limit; ?></label><div class="col-sm-9"><input type="number" min="1" name="egeser_product_limit" value="<?php echo (int)$egeser_product_limit; ?>" class="form-control"></div></div>
<div class="form-group"><label class="col-sm-3 control-label"><?php echo $entry_description_length; ?></label><div class="col-sm-9"><input type="number" min="1" name="egeser_product_description_length" value="<?php echo (int)$egeser_product_description_length; ?>" class="form-control"></div></div>
<hr><h4><?php echo $entry_image_sizes; ?></h4>
<div class="form-group">
<label class="col-sm-3 control-label">Kategori</label>
<div class="col-sm-9"><div class="row">
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_category_width" value="<?php echo (int)$egeser_image_category_width; ?>" class="form-control" placeholder="Genişlik"></div>
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_category_height" value="<?php echo (int)$egeser_image_category_height; ?>" class="form-control" placeholder="Yükseklik"></div>
</div></div></div>
<div class="form-group">
<label class="col-sm-3 control-label">Ürün Büyük Görsel</label>
<div class="col-sm-9"><div class="row">
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_thumb_width" value="<?php echo (int)$egeser_image_thumb_width; ?>" class="form-control" placeholder="Genişlik"></div>
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_thumb_height" value="<?php echo (int)$egeser_image_thumb_height; ?>" class="form-control" placeholder="Yükseklik"></div>
</div></div></div>
<div class="form-group">
<label class="col-sm-3 control-label">Popup</label>
<div class="col-sm-9"><div class="row">
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_popup_width" value="<?php echo (int)$egeser_image_popup_width; ?>" class="form-control" placeholder="Genişlik"></div>
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_popup_height" value="<?php echo (int)$egeser_image_popup_height; ?>" class="form-control" placeholder="Yükseklik"></div>
</div></div></div>
<div class="form-group">
<label class="col-sm-3 control-label">Ürün Kartı</label>
<div class="col-sm-9"><div class="row">
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_product_width" value="<?php echo (int)$egeser_image_product_width; ?>" class="form-control" placeholder="Genişlik"></div>
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_product_height" value="<?php echo (int)$egeser_image_product_height; ?>" class="form-control" placeholder="Yükseklik"></div>
</div></div></div>
<div class="form-group">
<label class="col-sm-3 control-label">Ek Görsel</label>
<div class="col-sm-9"><div class="row">
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_additional_width" value="<?php echo (int)$egeser_image_additional_width; ?>" class="form-control" placeholder="Genişlik"></div>
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_additional_height" value="<?php echo (int)$egeser_image_additional_height; ?>" class="form-control" placeholder="Yükseklik"></div>
</div></div></div>
<div class="form-group">
<label class="col-sm-3 control-label">Benzer Ürün</label>
<div class="col-sm-9"><div class="row">
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_related_width" value="<?php echo (int)$egeser_image_related_width; ?>" class="form-control" placeholder="Genişlik"></div>
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_related_height" value="<?php echo (int)$egeser_image_related_height; ?>" class="form-control" placeholder="Yükseklik"></div>
</div></div></div>
<div class="form-group">
<label class="col-sm-3 control-label">Karşılaştırma</label>
<div class="col-sm-9"><div class="row">
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_compare_width" value="<?php echo (int)$egeser_image_compare_width; ?>" class="form-control" placeholder="Genişlik"></div>
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_compare_height" value="<?php echo (int)$egeser_image_compare_height; ?>" class="form-control" placeholder="Yükseklik"></div>
</div></div></div>
<div class="form-group">
<label class="col-sm-3 control-label">İstek Listesi</label>
<div class="col-sm-9"><div class="row">
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_wishlist_width" value="<?php echo (int)$egeser_image_wishlist_width; ?>" class="form-control" placeholder="Genişlik"></div>
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_wishlist_height" value="<?php echo (int)$egeser_image_wishlist_height; ?>" class="form-control" placeholder="Yükseklik"></div>
</div></div></div>
<div class="form-group">
<label class="col-sm-3 control-label">Sepet</label>
<div class="col-sm-9"><div class="row">
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_cart_width" value="<?php echo (int)$egeser_image_cart_width; ?>" class="form-control" placeholder="Genişlik"></div>
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_cart_height" value="<?php echo (int)$egeser_image_cart_height; ?>" class="form-control" placeholder="Yükseklik"></div>
</div></div></div>
<div class="form-group">
<label class="col-sm-3 control-label">Mağaza Konumu</label>
<div class="col-sm-9"><div class="row">
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_location_width" value="<?php echo (int)$egeser_image_location_width; ?>" class="form-control" placeholder="Genişlik"></div>
<div class="col-xs-6"><input type="number" min="1" name="egeser_image_location_height" value="<?php echo (int)$egeser_image_location_height; ?>" class="form-control" placeholder="Yükseklik"></div>
</div></div></div>
</form></div></div></div></div>
<?php echo $footer; ?>
