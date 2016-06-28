<?php

include_once "FlotChartWidget.php";

class FlotChartBurndownWidget extends FlotChartWidget
{
    var $url;
    
    public function setUrl( $url )
    {
        $this->url = $url;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function draw( $chart_id, $sectionMode = false )
    {
		if ( $sectionMode ) {
			?>
			<script type="text/javascript">
				$(document).on('tabsactivated', function(event, e,ui) {
					if ( $(ui.tab).attr('href').indexOf('burndownsection') > -1 ) {
						$("#<?=$chart_id?>").css({
							width: '100%',
							height: $(window).height() * 0.5
						});

						<? $this->drawChart($chart_id) ?>
					}
				});
			</script>
			<?
		}
		else {
			?>
			<script type="text/javascript">
				$(function () {
					<? $this->drawChart($chart_id) ?>
				});
			</script>
			<?
		}
    }

	protected function drawChart( $chart_id )
	{
		?>
		$.ajax({
			url: '<?=$this->getUrl()?>',
			dataType: 'json',
			error: function( xhr, status, e )
			{
			},
			success: function( data )
			{
				if ( data == null ) data = [];
				$.plot($("#<?=$chart_id?>"), data,
					{
						colors: ["rgb(225,63,63)", "rgb(247,239,59)", "rgb(63,225,63)", "rgb(63,63,225)"],
						series: {
							lines: {
								show: true,
								fill: false
							},
							points: {
								show: false
							}
						},
						legend: {
							show: false,
							position: 'nw'
						},
						xaxis: { mode: 'time', tickDecimals: 0, color: 'rgb(192,192,192)', tickLength:0 },
						yaxis: { tickDecimals: 0, color: 'rgb(192,192,192)', tickLength:0 },
						grid: {
							borderWidth: 1,
							hoverable: true,
							clickable: true,
							borderColor: 'rgb(192,192,192)'
						}
					});
			}
		});
		<?
	}
}