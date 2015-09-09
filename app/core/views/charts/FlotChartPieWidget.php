<?php

include_once "FlotChartWidget.php";

class FlotChartPieWidget extends FlotChartWidget
{
    function getValues()
    {
        $rows = array();
        
		foreach ( $this->getData() as $key => $item )
		{
			$rows[] = "{ label: '".$key."', data: ".$item['data']." }";
		}
		
		return $rows;
    }
    
    public function draw( $chart_id )
    {
		?>
		<script type="text/javascript">
		$(function () 
		{
			var data = [<?=join($this->getValues(), ',')?>];
			
			$.plot($("#<?=$chart_id?>"), data, 
			{
				series: {
					pie: { 
						show: true,
						label: {
							show: true
						},
						innerRadius: 0.3
					}
				},
				legend: {
					show: false
				},
				radius: 1
				<? if ( count($this->getColors()) > 0 ) { ?> ,colors: ['<?=join("','",$this->getColors())?>'] <? } ?>
			});
		});
		</script>
		<?
        
    }
}