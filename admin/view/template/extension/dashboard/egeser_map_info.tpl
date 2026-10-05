<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><i class="fa fa-map-marker"></i> <?php echo $heading_title; ?> — <?php echo $visitor_count; ?> ziyaretçi (son 30 gün)</h3>
  </div>
  <div class="panel-body">
    <?php include(DIR_TEMPLATE . 'common/egeser_il_map.tpl'); ?>
    <p class="text-right" style="margin-top:8px;"><a href="<?php echo $report_url; ?>"><i class="fa fa-bar-chart"></i> Tüm ziyaretçi raporları &raquo;</a></p>
  </div>
</div>
