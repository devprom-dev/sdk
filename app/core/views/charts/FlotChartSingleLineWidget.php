<?php

include_once "FlotChartWidget.php";

class FlotChartSingleLineWidget extends FlotChartWidget
{
    function getValues()
    {
		$index = 0;
		
		$data = array();

		foreach ( $this->getData() as $key => $item )
		{
		    $data[] = array( $index, $item['data'] );
			
			$index++;
		}

		$labels = array();
		
		foreach( array_keys($this->getData()) as $key => $tick )
		{
			array_push( $labels, "[".$key.", '".$tick."']");
		}

		return array( $labels, $data );
    }
    
    public function draw( $chart_id )
    {
        list( $ticks, $data ) = $this->getValues();

		?>
		<script type="text/javascript">
		$(function () {
			var data = [<?=JsonWrapper::encode($data)?>];
			var ticks = [<?=join($ticks, ',')?>];

			$.plot($("#<?=$chart_id?>"), data, 
			{
				series: {
					stack: true,
					lines: { 
						show: true,
						fill: true
					},
					points: {
						show: true
					}
				},
				legend: {
					show: <?=($this->getShowLegend() ? 'true' : 'false')?>,
				},
				xaxis: {
					ticks: ticks,
					autoscaleMargin: 0.4,
					alignTicksWithAxis: 0.2
				},
				grid: {
					hoverable: true,
					clickable: true
				}
				<? if ( count($this->getColors()) > 0 ) { ?> ,colors: ['<?=join("','",$this->getColors())?>'] <? } ?>
			});
		});			
		</script>
		<?
    }
}