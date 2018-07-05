<?php
include_once "FlotChartWidget.php";

class FlotChartMultiLineWidget extends FlotChartWidget
{
    function getValues()
    {
    	$rows = array();
		$inner_row = array();
		$sourceData = $this->getData();

		foreach ( $sourceData as $item_key => $item ) {
			foreach ( $item['data'] as $data_key => $data ) {
				$inner_row[$data_key][$item_key] += $data;
			}
		}

		foreach ( $inner_row as $key => $item )
		{
			$row = array();
			foreach ( $item as $inner_key => $inner ) {
				array_push($row, "[".count($row).", ".$inner."]");
			}
			array_push( $rows, " {label: '".$key."', data: [".join($row, ',')."] }");
		}

        $ticks = array();
		$index = 0;
        foreach( array_keys($sourceData) as $key ) {
            $ticks[] = "[".$index.", '".$key."']";
            $index++;
        }
		return array($ticks, $rows);
    }
    
    public function draw( $chart_id )
    {
        list($ticks, $data) = $this->getValues();
		?>
		<script type="text/javascript">
		$(function () {
			var data = [<?=join($data, ',')?>];
            var ticks = [<?=join($ticks, ',')?>];

			$.plot($("#<?=$chart_id?>"), data, 
			{
				series: {
					stack: false,
					lines: { 
						show: true
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
                    ticks: ticks,
                    alignTicksWithAxis: 0.2
                },
				grid: {
					hoverable: true,
					clickable: true,
					borderColor: 'rgb(192,192,192)'
				}
				<? if ( count($this->getColors()) > 0 ) { ?> ,colors: ['<?=join("','",$this->getColors())?>'] <? } ?>
			});
	    });
		</script>
		<?
    }
}