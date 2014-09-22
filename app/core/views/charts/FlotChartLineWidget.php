<?php

include_once "FlotChartWidget.php";

class FlotChartLineWidget extends FlotChartWidget
{
    function getValues()
    {
    	$rows = array();
		$index = 0;
		
		$inner_row = array();
		
		foreach ( $this->getData() as $item_key => $item )
		{
			foreach ( $item['data'] as $data_key => $data )
			{
				$inner_row[$data_key][$item_key] += $data;
			}
		}

		foreach ( $inner_row as $key => $item )
		{
			$row = array();
			$index = 0;
			
			foreach ( $item as $inner_key => $inner )
			{
				array_push($row, "[".($inner_key*1000).", ".$inner."]");
			}
			
			array_push( $rows, " {label: '".$key."', data: [".join($row, ',')."] }");
		}
        
		return $rows;
    }
    
    public function draw( $chart_id )
    {
		?>
		<script type="text/javascript">
		$(function () {
			var data = [<?=join($this->getValues(), ',')?>];

			$.plot($("#<?=$chart_id?>"), data, 
			{
				series: {
					stack: true,
					lines: { 
						show: true,
						fill: true
					},
					points: {
						show: <? echo $this->getShowPoints() ? 'true' : 'false'; ?>
					}
				},
				legend: {
					show: <? echo $this->getShowLegend() ? 'true' : 'false'?>,
					position: 'nw'
				},
				xaxis: {
					mode: 'time'
				},
				grid: {
					hoverable: true,
					clickable: true,
					borderColor: 'rgb(192,192,192)'
				}
			});
	    });
		</script>
		<?
    }
}