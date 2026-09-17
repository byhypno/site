<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-egeser-visual" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div><?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-picture-o"></i> <?php echo $text_edit; ?></h3></div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-egeser-visual" class="form-horizontal">
          <div class="row" style="margin-bottom:20px">
            <div class="col-sm-4">
              <label><?php echo $entry_status; ?></label>
              <select name="egeser_visual_manager_status" class="form-control">
                <option value="1" <?php echo $egeser_visual_manager_status ? 'selected' : ''; ?>><?php echo $text_enabled; ?></option>
                <option value="0" <?php echo !$egeser_visual_manager_status ? 'selected' : ''; ?>><?php echo $text_disabled; ?></option>
              </select>
            </div>
            <div class="col-sm-4">
              <label><?php echo $entry_preview_only; ?></label>
              <select name="egeser_visual_manager_preview_only" class="form-control">
                <option value="1" <?php echo $egeser_visual_manager_preview_only ? 'selected' : ''; ?>><?php echo $text_yes; ?></option>
                <option value="0" <?php echo !$egeser_visual_manager_preview_only ? 'selected' : ''; ?>><?php echo $text_no; ?></option>
              </select>
              <p class="help-block"><?php echo $help_preview_only; ?></p>
            </div>
            <div class="col-sm-4">
              <div class="well well-sm" style="margin-top:24px"><b>Güvenli çalışma:</b><br>Bu modül çekirdek dosyaları, mail, session, 301 veya ürün tablolarını değiştirmez.</div>
            </div>
          </div>

          <div id="visual-rules">
            <?php $row=0; foreach ($rules as $rule) { ?>
            <div class="panel panel-default visual-rule" data-row="<?php echo $row; ?>">
              <div class="panel-heading clearfix">
                <strong><i class="fa fa-image"></i> <?php echo isset($rule['label']) ? htmlspecialchars($rule['label']) : 'Görsel Alanı'; ?></strong>
                <button type="button" class="btn btn-danger btn-xs pull-right btn-remove-rule"><i class="fa fa-trash"></i> <?php echo $button_remove; ?></button>
              </div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-sm-3 text-center">
                    <a href="" id="thumb-image-<?php echo $row; ?>" data-toggle="image" class="img-thumbnail" style="display:inline-block"><img src="<?php echo $rule['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" style="max-width:180px;max-height:100px" /></a>
                    <input type="hidden" name="egeser_visual_manager_rules[<?php echo $row; ?>][image]" value="<?php echo isset($rule['image']) ? htmlspecialchars($rule['image']) : ''; ?>" id="input-image-<?php echo $row; ?>" />
                    <div class="small text-muted" style="margin-top:8px">Mevcut: <?php echo $rule['dimension']; ?></div>
                    <div class="small"><b><?php echo $entry_recommended; ?>:</b> <?php echo isset($rule['recommended']) ? htmlspecialchars($rule['recommended']) : '-'; ?></div>
                  </div>
                  <div class="col-sm-9">
                    <div class="row">
                      <div class="col-sm-5 form-group"><label><?php echo $entry_label; ?></label><input class="form-control" name="egeser_visual_manager_rules[<?php echo $row; ?>][label]" value="<?php echo isset($rule['label']) ? htmlspecialchars($rule['label']) : ''; ?>"></div>
                      <div class="col-sm-3 form-group"><label><?php echo $entry_rule_status; ?></label><select class="form-control" name="egeser_visual_manager_rules[<?php echo $row; ?>][status]"><option value="1" <?php echo !empty($rule['status'])?'selected':''; ?>><?php echo $text_enabled; ?></option><option value="0" <?php echo empty($rule['status'])?'selected':''; ?>><?php echo $text_disabled; ?></option></select></div>
                      <div class="col-sm-4 form-group"><label><?php echo $entry_match_type; ?></label><select class="form-control" name="egeser_visual_manager_rules[<?php echo $row; ?>][match_type]"><option value="text" <?php echo (isset($rule['match_type'])&&$rule['match_type']=='text')?'selected':''; ?>><?php echo $text_textmatch; ?></option><option value="selector" <?php echo (isset($rule['match_type'])&&$rule['match_type']=='selector')?'selected':''; ?>><?php echo $text_selector; ?></option></select></div>
                    </div>
                    <div class="row">
                      <div class="col-sm-8 form-group"><label><?php echo $entry_target; ?></label><input class="form-control" name="egeser_visual_manager_rules[<?php echo $row; ?>][target]" value="<?php echo isset($rule['target']) ? htmlspecialchars($rule['target']) : ''; ?>"><p class="help-block"><?php echo $help_target; ?></p></div>
                      <div class="col-sm-4 form-group"><label><?php echo $entry_apply_type; ?></label><select class="form-control" name="egeser_visual_manager_rules[<?php echo $row; ?>][apply_type]"><option value="img" <?php echo (isset($rule['apply_type'])&&$rule['apply_type']=='img')?'selected':''; ?>><?php echo $text_imgsrc; ?></option><option value="background" <?php echo (isset($rule['apply_type'])&&$rule['apply_type']=='background')?'selected':''; ?>><?php echo $text_background; ?></option></select></div>
                    </div>
                    <div class="row">
                      <div class="col-sm-4 form-group"><label><?php echo $entry_alt; ?></label><input class="form-control" name="egeser_visual_manager_rules[<?php echo $row; ?>][alt]" value="<?php echo isset($rule['alt']) ? htmlspecialchars($rule['alt']) : ''; ?>"></div>
                      <div class="col-sm-4 form-group"><label><?php echo $entry_fit; ?></label><select class="form-control" name="egeser_visual_manager_rules[<?php echo $row; ?>][fit]"><option value="cover" <?php echo (!isset($rule['fit'])||$rule['fit']=='cover')?'selected':''; ?>><?php echo $text_cover; ?></option><option value="contain" <?php echo (isset($rule['fit'])&&$rule['fit']=='contain')?'selected':''; ?>><?php echo $text_contain; ?></option></select></div>
                      <div class="col-sm-4 form-group"><label><?php echo $entry_position; ?></label><input class="form-control" name="egeser_visual_manager_rules[<?php echo $row; ?>][position]" value="<?php echo isset($rule['position']) ? htmlspecialchars($rule['position']) : '50% 50%'; ?>" placeholder="50% 50%"></div>
                    </div>
                    <input type="hidden" name="egeser_visual_manager_rules[<?php echo $row; ?>][recommended]" value="<?php echo isset($rule['recommended']) ? htmlspecialchars($rule['recommended']) : ''; ?>">
                  </div>
                </div>
              </div>
            </div>
            <?php $row++; } ?>
          </div>

          <button type="button" id="button-add-rule" class="btn btn-success"><i class="fa fa-plus"></i> <?php echo $button_add_rule; ?></button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
var visualRow = <?php echo $row; ?>;
$('#visual-rules').on('click','.btn-remove-rule',function(){ $(this).closest('.visual-rule').remove(); });
$('#button-add-rule').on('click', function(){
  var r = visualRow++;
  var html = ''+
  '<div class="panel panel-default visual-rule" data-row="'+r+'">'+
    '<div class="panel-heading clearfix"><strong><i class="fa fa-image"></i> Yeni Görsel Alanı</strong><button type="button" class="btn btn-danger btn-xs pull-right btn-remove-rule"><i class="fa fa-trash"></i> <?php echo addslashes($button_remove); ?></button></div>'+
    '<div class="panel-body"><div class="row">'+
      '<div class="col-sm-3 text-center"><a href="" id="thumb-image-'+r+'" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" data-placeholder="<?php echo $placeholder; ?>" style="max-width:180px;max-height:100px"></a><input type="hidden" id="input-image-'+r+'" name="egeser_visual_manager_rules['+r+'][image]" value=""><div class="small text-muted" style="margin-top:8px">Mevcut: -</div></div>'+
      '<div class="col-sm-9">'+
        '<div class="row"><div class="col-sm-5 form-group"><label><?php echo addslashes($entry_label); ?></label><input class="form-control" name="egeser_visual_manager_rules['+r+'][label]" value="Yeni Görsel Alanı"></div><div class="col-sm-3 form-group"><label><?php echo addslashes($entry_rule_status); ?></label><select class="form-control" name="egeser_visual_manager_rules['+r+'][status]"><option value="1"><?php echo addslashes($text_enabled); ?></option><option value="0"><?php echo addslashes($text_disabled); ?></option></select></div><div class="col-sm-4 form-group"><label><?php echo addslashes($entry_match_type); ?></label><select class="form-control" name="egeser_visual_manager_rules['+r+'][match_type]"><option value="text"><?php echo addslashes($text_textmatch); ?></option><option value="selector"><?php echo addslashes($text_selector); ?></option></select></div></div>'+
        '<div class="row"><div class="col-sm-8 form-group"><label><?php echo addslashes($entry_target); ?></label><input class="form-control" name="egeser_visual_manager_rules['+r+'][target]" value=""></div><div class="col-sm-4 form-group"><label><?php echo addslashes($entry_apply_type); ?></label><select class="form-control" name="egeser_visual_manager_rules['+r+'][apply_type]"><option value="img"><?php echo addslashes($text_imgsrc); ?></option><option value="background"><?php echo addslashes($text_background); ?></option></select></div></div>'+
        '<div class="row"><div class="col-sm-4 form-group"><label><?php echo addslashes($entry_alt); ?></label><input class="form-control" name="egeser_visual_manager_rules['+r+'][alt]" value=""></div><div class="col-sm-4 form-group"><label><?php echo addslashes($entry_fit); ?></label><select class="form-control" name="egeser_visual_manager_rules['+r+'][fit]"><option value="cover"><?php echo addslashes($text_cover); ?></option><option value="contain"><?php echo addslashes($text_contain); ?></option></select></div><div class="col-sm-4 form-group"><label><?php echo addslashes($entry_position); ?></label><input class="form-control" name="egeser_visual_manager_rules['+r+'][position]" value="50% 50%"></div></div>'+
        '<input type="hidden" name="egeser_visual_manager_rules['+r+'][recommended]" value="">'+
      '</div></div></div></div>';
  $('#visual-rules').append(html);
});
</script>
<style>
#form-egeser-visual .visual-rule{border-radius:8px;overflow:hidden}.visual-rule .panel-heading{background:#f7f9fb}.visual-rule .form-group{padding-left:8px;padding-right:8px}.visual-rule .img-thumbnail{background:#fff;min-width:182px;min-height:102px;display:flex!important;align-items:center;justify-content:center}.visual-rule .small{line-height:1.6}
</style>
<?php echo $footer; ?>
