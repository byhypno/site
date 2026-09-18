<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div><?php } ?>
    <?php if ($success) { ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div><?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-picture-o"></i> <?php echo $heading_title; ?></h3></div>
      <div class="panel-body">
        <p><?php echo $text_intro; ?></p>

        <div class="row">
          <?php foreach ($slots as $slot) { ?>
          <div class="col-sm-4" style="margin-bottom: 24px;">
            <div class="panel panel-default" style="margin-bottom: 0;">
              <div class="panel-body" style="text-align: center;">
                <?php if ($slot['exists']) { ?>
                <img src="<?php echo $slot['image']; ?>" style="max-width: 100%; max-height: 180px; border-radius: 8px; margin-bottom: 12px;">
                <?php } else { ?>
                <div style="height: 120px; display: flex; align-items: center; justify-content: center; background: #f5f5f5; border-radius: 8px; margin-bottom: 12px; color: #999;">
                  <i class="fa fa-picture-o fa-2x"></i>
                </div>
                <?php } ?>
                <div style="font-weight: bold; margin-bottom: 4px;"><?php echo $slot['label']; ?></div>
                <div style="color: #999; font-size: 12px; margin-bottom: 12px;"><?php echo $slot['help']; ?></div>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="slot" value="<?php echo $slot['key']; ?>">
                  <div class="form-group" style="margin-bottom: 8px;">
                    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required>
                  </div>
                  <button type="submit" class="btn btn-primary btn-sm btn-block"><i class="fa fa-upload"></i> Görseli Değiştir</button>
                </form>
              </div>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
