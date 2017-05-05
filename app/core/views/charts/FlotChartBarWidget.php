<?php

include_once "FlotChartWidget.php";

class FlotChartBarWidget extends FlotChartWidget
{
    function getValues()
    {
    	$rows = array();
		$ticks = array();
		$index = 0;
		
		$data = $this->getData();
		
		$keys = array_keys($data);
		
		if ( !is_array($data[$keys[0]]['data']) )
		{
			foreach ( $data as $key => $item )
			{
				array_push( $rows, "{ label: '".$key."', data:[[".$index.", 0],[".$index.",".$item['data']."]] }" );
				$index++;
			}
		}
		else
		{
			$inner_row = array();
			foreach ( $data as $item_key => $item )
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
					array_push($row, "[".$index.", ".$inner."]");
					$ticks[$index] = $inner_key;
					
					$index++;
				}
				
				array_push( $rows, " {label: '".$key."', data: [".join($row, ',')."] }");
			}
		}

		$labels = array();
		
		foreach( $ticks as $key => $tick )
		{
			array_push( $labels, "[".$key.", '".$tick."']");
		}

		return array( $labels, $rows );
    }
    
    public function draw( $chart_id )
    {
        list( $ticks, $data ) = $this->getValues();
        $scaleDivider = count($ticks) > 0 ? count($ticks) : 1;
		?>
		<script type="text/javascript">
		$(function () {
			var data = [<?=join($data, ',')?>];
			var ticks = [<?=join($ticks, ',')?>];

			$.plot($("#<?=$chart_id?>"), data, 
			{
				series: {
					stack: true,
					bars: { 
						show: true,
						barWidth: 0.3
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
					autoscaleMargin: <?=(10/($scaleDivider*$scaleDivider))?>,
                    rotateTicks: 135,
                    reserveSpace: true,
                    labelWidth: 15
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