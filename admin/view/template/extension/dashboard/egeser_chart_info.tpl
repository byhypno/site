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
    <div id="egeser-chart" style="width: 100%; height: 260px;"></div>
  </div>
</div>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.js"></script>
<script type="text/javascript" src="view/javascript/jquery/flot/jquery.flot.resize.min.js"></script>
<script type="text/javascript"><!--
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
			var option = {
				shadowSize: 0,
				colors: ['#1065D2', '#9FD5F1'],
				bars: { show: true, fill: true, lineWidth: 1 },
				grid: { backgroundColor: '#FFFFFF', hoverable: true },
				points: { show: false },
				xaxis: { show: true, ticks: json['xaxis'] }
			};

			$.plot('#egeser-chart', [json['visitors'], json['product_views']], option);

			$('#egeser-chart').bind('plothover', function(event, pos, item) {
				$('.tooltip').remove();

				if (item) {
					$('<div id="tooltip" class="tooltip top in"><div class="tooltip-arrow"></div><div class="tooltip-inner">' + item.datapoint[1] + '</div></div>').prependTo('body');

					$('#tooltip').css({
						position: 'absolute',
						left: item.pageX - ($('#tooltip').outerWidth() / 2),
						top: item.pageY - $('#tooltip').outerHeight()
					}).fadeIn('slow');

					$('#egeser-chart').css('cursor', 'pointer');
				} else {
					$('#egeser-chart').css('cursor', 'auto');
				}
			});
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#egeser-chart-range .active a').trigger('click');
//--></script>
