<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-egeser-hero-slider" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div><?php } ?>
    <?php if ($error_slides) { ?><div class="alert alert-warning"><i class="fa fa-picture-o"></i> <?php echo $error_slides; ?></div><?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-picture-o"></i> <?php echo $text_edit; ?></h3></div>
      <div class="panel-body">
        <div class="alert alert-info"><strong>Egeser Hero:</strong> Buradan 1–6 görsel seçebilirsiniz. Modülü kaydettikten sonra <b>Tasarım → Bölümler → Home → Orta Üste</b> ekleyin. Modül görünür bir blok oluşturmaz; ana sayfadaki mevcut gri “Ana proje görseli” alanını otomatik olarak slider'a çevirir.</div>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-egeser-hero-slider" class="form-horizontal">
          <div class="form-group required"><label class="col-sm-2 control-label"><?php echo $entry_name; ?></label><div class="col-sm-10"><input type="text" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" class="form-control"><?php if ($error_name) { ?><div class="text-danger"><?php echo $error_name; ?></div><?php } ?></div></div>
          <div class="form-group"><label class="col-sm-2 control-label"><?php echo $entry_interval; ?></label><div class="col-sm-4"><input type="number" min="2500" max="15000" step="100" name="interval" value="<?php echo (int)$interval; ?>" class="form-control"><p class="help-block"><?php echo $help_interval; ?></p></div><label class="col-sm-2 control-label"><?php echo $entry_transition; ?></label><div class="col-sm-4"><input type="number" min="250" max="2500" step="50" name="transition" value="<?php echo (int)$transition; ?>" class="form-control"><p class="help-block"><?php echo $help_transition; ?></p></div></div>
          <div class="form-group"><label class="col-sm-2 control-label"><?php echo $entry_pause_hover; ?></label><div class="col-sm-4"><select name="pause_hover" class="form-control"><option value="1" <?php echo $pause_hover ? 'selected="selected"' : ''; ?>><?php echo $text_yes; ?></option><option value="0" <?php echo !$pause_hover ? 'selected="selected"' : ''; ?>><?php echo $text_no; ?></option></select></div><label class="col-sm-2 control-label"><?php echo $entry_status; ?></label><div class="col-sm-4"><select name="status" class="form-control"><option value="1" <?php echo $status ? 'selected="selected"' : ''; ?>><?php echo $text_enabled; ?></option><option value="0" <?php echo !$status ? 'selected="selected"' : ''; ?>><?php echo $text_disabled; ?></option></select></div></div>
          <?php foreach ($slides as $i => $slide) { ?>
          <div class="panel panel-default" style="margin-top:18px">
            <div class="panel-heading"><strong>Hero Görseli <?php echo $i + 1; ?></strong><label class="pull-right" style="font-weight:normal"><input type="checkbox" name="slides[<?php echo $i; ?>][enabled]" value="1" <?php echo !empty($slide['enabled']) ? 'checked="checked"' : ''; ?>> Aktif</label></div>
            <div class="panel-body"><div class="row">
              <div class="col-sm-3"><a href="" id="thumb-slide-<?php echo $i; ?>" data-toggle="image" class="img-thumbnail" style="display:block;max-width:240px"><img src="<?php echo $slide['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" style="max-width:100%"></a><input type="hidden" name="slides[<?php echo $i; ?>][image]" value="<?php echo htmlspecialchars($slide['image'], ENT_QUOTES, 'UTF-8'); ?>" id="input-slide-<?php echo $i; ?>"></div>
              <div class="col-sm-9">
                <div class="form-group"><label class="col-sm-2 control-label">Alt Metni</label><div class="col-sm-10"><input type="text" name="slides[<?php echo $i; ?>][alt]" value="<?php echo htmlspecialchars($slide['alt'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Örn. 85 m² prefabrik ev projesi"></div></div>
                <div class="form-group"><label class="col-sm-2 control-label">Bağlantı</label><div class="col-sm-10"><input type="text" name="slides[<?php echo $i; ?>][link]" value="<?php echo htmlspecialchars($slide['link'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="İsteğe bağlı: /85-m2-prefabrik-ev-2-1"></div></div>
                <div class="form-group"><label class="col-sm-2 control-label">Sıra</label><div class="col-sm-3"><input type="number" name="slides[<?php echo $i; ?>][sort_order]" value="<?php echo (int)$slide['sort_order']; ?>" class="form-control"></div></div>
              </div>
            </div></div>
          </div>
          <?php } ?>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
