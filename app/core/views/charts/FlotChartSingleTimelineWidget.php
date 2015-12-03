<?php

include_once "FlotChartSingleLineWidget.php";

class FlotChartSingleTimelineWidget extends FlotChartSingleLineWidget
{
	private $label = '';

	function setLabel( $label ) {
		$this->label = $label;
	}

    function getValues()
    {
		$data = array();

		foreach ( $this->getData() as $key => $item ) {
		    $data[] = array($key*1000, $item['data']);
		}

		return $data;
    }

    public function draw( $chart_id )
    {
		?>
		<script type="text/javascript">
		$(function () {
			var data = [{label: '<?=$this->label?>', data: <?=JsonWrapper::encode($this->getValues())?>}];
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
					mode: 'time'
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