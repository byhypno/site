<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><i class="fa fa-share-alt"></i> <?php echo $heading_title; ?> <small class="text-muted">(son 30 gün)</small></h3>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-sm-5">
        <div id="egeser-sources-pie" style="width:100%;height:180px;"></div>
      </div>
      <div class="col-sm-7">
        <table class="table table-condensed" style="margin-bottom:0;">
          <thead><tr><th>Kaynak</th><th class="text-right">Oturum</th><th class="text-right">Dönüşüm %</th></tr></thead>
          <tbody>
          <?php if ($sources) { foreach ($sources as $s) { ?>
            <tr>
              <td><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:<?php echo $s['color']; ?>;margin-right:6px;"></span><?php echo htmlspecialchars($s['label'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td class="text-right"><?php echo (int)$s['sessions']; ?></td>
              <td class="text-right"><?php echo $s['conversion_rate']; ?></td>
            </tr>
          <?php } } else { ?>
            <tr><td colspan="3" class="text-center text-muted">Veri yok.</td></tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="panel-footer text-right">
    <a href="<?php echo $report_url; ?>"><i class="fa fa-list"></i> Tüm Kaynaklar &raquo;</a>
  </div>
</div>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.js"></script>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.pie.js"></script>
<script type="text/javascript"><!--
$(function() {
	var data = [
		<?php foreach ($sources as $s) { ?>
		{ label: '<?php echo addslashes($s['label']); ?>', data: <?php echo (int)$s['sessions']; ?>, color: '<?php echo $s['color']; ?>' },
		<?php } ?>
	];

	if (data.length) {
		$.plot('#egeser-sources-pie', data, {
			series: {
				pie: {
					show: true,
					radius: 0.9,
					label: { show: false }
				}
			},
			legend: { show: false },
			grid: { hoverable: true }
		});
	}
});
//--></script>
