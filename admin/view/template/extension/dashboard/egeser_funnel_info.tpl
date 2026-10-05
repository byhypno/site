<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><i class="fa fa-filter"></i> <?php echo $heading_title; ?> <small class="text-muted">(son 30 gün)</small></h3>
  </div>
  <div class="panel-body">
    <?php if ($stages) { foreach ($stages as $stage) { ?>
    <div style="margin-bottom:12px;">
      <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px;">
        <span><?php echo htmlspecialchars($stage['label'], ENT_QUOTES, 'UTF-8'); ?></span>
        <strong><?php echo (int)$stage['count']; ?></strong>
      </div>
      <div style="background:#eef2f6;border-radius:3px;height:18px;overflow:hidden;">
        <div style="background:#2f6aa8;height:100%;width:<?php echo (int)$stage['percent']; ?>%;border-radius:3px;"></div>
      </div>
    </div>
    <?php } } else { ?>
    <p class="text-center text-muted">Veri yok.</p>
    <?php } ?>
    <p class="text-muted" style="font-size:11px;margin-bottom:0;">Her aşama, o aşamayı en az bir kez yaşayan benzersiz ziyaretçi sayısıdır (kesin sıralı bir huni değildir — örn. formu dolduran biri ürün sayfasına hiç girmemiş olabilir).</p>
  </div>
</div>
