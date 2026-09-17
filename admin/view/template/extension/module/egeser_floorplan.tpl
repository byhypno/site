<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header"><div class="container-fluid">
    <div class="pull-right"><button type="submit" form="form-egeser-floorplan" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button><a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
    <h1><?php echo $heading_title; ?></h1>
    <ul class="breadcrumb"><?php foreach ($breadcrumbs as $breadcrumb) { ?><li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li><?php } ?></ul>
  </div></div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div><?php } ?>
    <?php if ($success) { ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div><?php } ?>
    <div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title"><i class="fa fa-building-o"></i> <?php echo $text_edit; ?></h3></div>
      <div class="panel-body">
        <div class="form-group"><label class="col-sm-2 control-label"><?php echo $entry_product; ?></label><div class="col-sm-10">
          <select id="eg-product-select" class="form-control">
            <option value="0">-- <?php echo $entry_product; ?> --</option>
            <?php foreach ($products as $product) { ?><option value="<?php echo (int)$product['product_id']; ?>"<?php echo ((int)$product['product_id']==(int)$product_id?' selected="selected"':''); ?>><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></option><?php } ?>
          </select><p class="help-block"><?php echo $text_select_product; ?></p>
        </div></div>
        <div style="clear:both"></div>
        <?php if ($product_id) { ?>
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-egeser-floorplan" class="form-horizontal">
          <input type="hidden" name="product_id" value="<?php echo (int)$product_id; ?>">
          <div class="table-responsive"><table class="table table-bordered table-hover" id="floorplan-table"><thead><tr><td><?php echo $entry_title; ?></td><td><?php echo $entry_image; ?></td><td style="width:120px"><?php echo $entry_sort_order; ?></td><td style="width:70px"></td></tr></thead><tbody>
          <?php $row=0; foreach ($floorplans as $plan) { ?>
            <tr id="floorplan-row<?php echo $row; ?>">
              <td><input type="text" name="floorplans[<?php echo $row; ?>][title]" value="<?php echo htmlspecialchars($plan['title'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Zemin Kat / 1. Kat"></td>
              <td><a href="" id="thumb-image<?php echo $row; ?>" data-toggle="image" class="img-thumbnail"><img src="<?php echo $plan['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>"></a><input type="hidden" name="floorplans[<?php echo $row; ?>][image]" value="<?php echo htmlspecialchars($plan['image'], ENT_QUOTES, 'UTF-8'); ?>" id="input-image<?php echo $row; ?>"></td>
              <td><input type="number" name="floorplans[<?php echo $row; ?>][sort_order]" value="<?php echo (int)$plan['sort_order']; ?>" class="form-control"></td>
              <td><button type="button" onclick="$('#floorplan-row<?php echo $row; ?>').remove();" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
            </tr>
          <?php $row++; } ?>
          </tbody><tfoot><tr><td colspan="3"></td><td><button type="button" onclick="addFloorplan();" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td></tr></tfoot></table></div>
        </form>
        <script>var floorplan_row=<?php echo (int)$row; ?>;var placeholder=<?php echo json_encode($placeholder); ?>;function addFloorplan(){var r=floorplan_row++;var html='<tr id="floorplan-row'+r+'"><td><input type="text" name="floorplans['+r+'][title]" value="" class="form-control" placeholder="Zemin Kat / 1. Kat"></td><td><a href="" id="thumb-image'+r+'" data-toggle="image" class="img-thumbnail"><img src="'+placeholder+'" data-placeholder="'+placeholder+'"></a><input type="hidden" name="floorplans['+r+'][image]" value="" id="input-image'+r+'"></td><td><input type="number" name="floorplans['+r+'][sort_order]" value="'+(r+1)+'" class="form-control"></td><td><button type="button" onclick="$(\'#floorplan-row'+r+'\').remove();" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td></tr>';$('#floorplan-table tbody').append(html);}document.getElementById('eg-product-select').addEventListener('change',function(){window.location=<?php echo json_encode(htmlspecialchars_decode($load_product_url)); ?>+'&product_id='+encodeURIComponent(this.value);});</script>
        <?php } else { ?><div class="alert alert-info"><?php echo $text_select_product; ?></div><script>document.getElementById('eg-product-select').addEventListener('change',function(){window.location=<?php echo json_encode(htmlspecialchars_decode($load_product_url)); ?>+'&product_id='+encodeURIComponent(this.value);});</script><?php } ?>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
