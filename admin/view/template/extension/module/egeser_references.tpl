<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-egeser-references" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
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

    <div class="panel panel-default">
      <div class="panel-heading"><h3 class="panel-title"><i class="fa fa-picture-o"></i> <?php echo $text_edit; ?></h3></div>
      <div class="panel-body">
        <div class="alert alert-info">
          Bu modül ana sayfa dosyalarını değiştirmez. Kaydettikten sonra Design → Layouts → Home bölümüne ekleyin.
        </div>

        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-egeser-references" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
              <?php if ($error_name) { ?><div class="text-danger"><?php echo $error_name; ?></div><?php } ?>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_title; ?></label>
            <div class="col-sm-10">
              <input type="text" name="title" value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_subtitle; ?></label>
            <div class="col-sm-10">
              <textarea name="subtitle" rows="2" class="form-control"><?php echo htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="status" class="form-control">
                <option value="1" <?php echo $status ? 'selected="selected"' : ''; ?>><?php echo $text_enabled; ?></option>
                <option value="0" <?php echo !$status ? 'selected="selected"' : ''; ?>><?php echo $text_disabled; ?></option>
              </select>
            </div>
          </div>

          <?php foreach ($projects as $i => $project) { ?>
          <div class="panel panel-default" style="margin-top:20px">
            <div class="panel-heading">
              <strong>Referans Kartı <?php echo $i + 1; ?></strong>
              <label class="pull-right" style="font-weight:normal">
                <input type="checkbox" name="projects[<?php echo $i; ?>][enabled]" value="1" <?php echo !empty($project['enabled']) ? 'checked="checked"' : ''; ?>> Aktif
              </label>
            </div>

            <div class="panel-body">
              <div class="row">
                <div class="col-sm-3">
                  <label>Görsel</label>
                  <a href="" id="thumb-project-<?php echo $i; ?>" data-toggle="image" class="img-thumbnail" style="display:block;max-width:220px">
                    <img src="<?php echo $project['thumb']; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" style="max-width:100%">
                  </a>
                  <input type="hidden" name="projects[<?php echo $i; ?>][image]" value="<?php echo htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8'); ?>" id="input-project-<?php echo $i; ?>">
                </div>

                <div class="col-sm-9">
                  <div class="row">
                    <div class="col-sm-4"><div class="form-group"><label>Üst Etiket</label><input type="text" name="projects[<?php echo $i; ?>][eyebrow]" value="<?php echo htmlspecialchars($project['eyebrow'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="BİREYSEL / KURUMSAL"></div></div>
                    <div class="col-sm-4"><div class="form-group"><label>Proje Tipi</label><input type="text" name="projects[<?php echo $i; ?>][type]" value="<?php echo htmlspecialchars($project['type'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Prefabrik Ev"></div></div>
                    <div class="col-sm-4"><div class="form-group"><label>Başlık</label><input type="text" name="projects[<?php echo $i; ?>][title]" value="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control"></div></div>
                  </div>

                  <div class="form-group">
                    <label>Kısa Açıklama</label>
                    <textarea name="projects[<?php echo $i; ?>][description]" rows="3" class="form-control"><?php echo htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                  </div>

                  <div class="row">
                    <div class="col-sm-4"><div class="form-group"><label>Konum</label><input type="text" name="projects[<?php echo $i; ?>][location]" value="<?php echo htmlspecialchars($project['location'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Bergama / İzmir"></div></div>
                    <div class="col-sm-3"><div class="form-group"><label>Alan</label><input type="text" name="projects[<?php echo $i; ?>][size]" value="<?php echo htmlspecialchars($project['size'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="74 m²"></div></div>
                    <div class="col-sm-5"><div class="form-group"><label>Bağlantı</label><input type="text" name="projects[<?php echo $i; ?>][link]" value="<?php echo htmlspecialchars($project['link'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="/projelerimiz veya tam URL"></div></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php } ?>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
