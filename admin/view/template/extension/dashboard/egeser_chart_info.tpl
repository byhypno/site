<div class="panel panel-default">
  <div class="panel-heading">
    <div class="pull-right"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-calendar"></i> <i class="caret"></i></a>
      <ul id="egeser-chart-range" class="dropdown-menu dropdown-menu-right">
        <li><a href="day"><?php echo $text_day; ?></a></li>
        <li><a href="week"><?php echo $text_week; ?></a></li>
        <li class="active"><a href="month"><?php echo $text_month; ?></a></li>
        <li><a href="year"><?php echo $text_year; ?></a></li>
      </ul>
    </div>
    <h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> <?php echo $heading_title; ?></h3>
  </div>
  <div class="panel-body">
    <div class="row">
      <div class="col-sm-6">
        <strong class="text-muted"><?php echo $text_visitors; ?></strong>
        <div id="egeser-chart-visitors" style="width: 100%; height: 220px;"></div>
      </div>
      <div class="col-sm-6">
        <strong class="text-muted"><?php echo $text_product_views; ?></strong>
        <div id="egeser-chart-products" style="width: 100%; height: 220px;"></div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.js"></script>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.resize.min.js"></script>
<script type="text/javascript"><!--
function egeserPlotSingle(id, series, color) {
	var option = {
		shadowSize: 0,
		colors: [color],
		bars: { show: true, fill: true, lineWidth: 1 },
		grid: { backgroundColor: '#FFFFFF', hoverable: true },
		points: { show: false },
		xaxis: { show: true, ticks: series.xaxis }
	};

	$.plot(id, [{ label: series.label, data: series.data }], option);

	$(id).off('plothover').on('plothover', function(event, pos, item) {
		$('.tooltip').remove();

		if (item) {
			$('<div id="tooltip" class="tooltip top in"><div class="tooltip-arrow"></div><div class="tooltip-inner">' + item.datapoint[1] + '</div></div>').prependTo('body');

			$('#tooltip').css({
				position: 'absolute',
				left: item.pageX - ($('#tooltip').outerWidth() / 2),
				top: item.pageY - $('#tooltip').outerHeight()
			}).fadeIn('slow');

			$(id).css('cursor', 'pointer');
		} else {
			$(id).css('cursor', 'auto');
		}
	});
}

$('#egeser-chart-range a').on('click', function(e) {
	e.preventDefault();

	$(this).parent().parent().find('li').removeClass('active');
	$(this).parent().addClass('active');

	$.ajax({
		type: 'get',
		url: 'index.php?route=extension/dashboard/egeser_chart/chart&token=<?php echo $token; ?>&range=' + $(this).attr('href'),
		dataType: 'json',
		success: function(json) {
			if (typeof json['visitors'] == 'undefined') { return false; }

			json['visitors'].xaxis = json['xaxis'];
			json['product_views'].xaxis = json['xaxis'];

			egeserPlotSingle('#egeser-chart-visitors', json['visitors'], '#1065D2');
			egeserPlotSingle('#egeser-chart-products', json['product_views'], '#9FD5F1');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#egeser-chart-range .active a').trigger('click');
//--></script>
