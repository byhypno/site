<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-egeser-lead-security-v102" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?><div class="alert alert-danger"><?php echo $error_warning; ?></div><?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-shield"></i> <?php echo $text_edit; ?></h3></div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" id="form-egeser-lead-security-v102" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="egeser_lead_security_v102_status" class="form-control">
                <option value="1"<?php echo $egeser_lead_security_v102_status ? ' selected="selected"' : ''; ?>><?php echo $text_enabled; ?></option>
                <option value="0"<?php echo !$egeser_lead_security_v102_status ? ' selected="selected"' : ''; ?>><?php echo $text_disabled; ?></option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_lifetime; ?></label>
            <div class="col-sm-10">
              <input type="number" min="5" max="60" name="egeser_lead_security_v102_lifetime" value="<?php echo (int)$egeser_lead_security_v102_lifetime; ?>" class="form-control">
              <p class="help-block"><?php echo $help_lifetime; ?></p>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
