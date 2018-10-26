<?php

include_once "FlotChartWidget.php";

class FlotChartPieWidget extends FlotChartWidget
{
    function getValues()
    {
        $rows = array();
        
		foreach ( $this->getData() as $key => $item ) {
			$rows[] = array(
                'label' => $key,
                'data' => $item['data']
            );
		}
		
		return $rows;
    }
    
    public function draw( $chart_id )
    {
        $availableColors = $this->getColors();
        $values = $this->getValues();
        foreach( $values as $value ) {
            $colors[] = $availableColors[$value['label']];
        }

		?>
		<script type="text/javascript">
		$(function () 
		{
			var data = <?=JsonWrapper::encode($values)?>;
			
			$.plot($("#<?=$chart_id?>"), data, 
			{
				width: 120,
				series: {
					pie: { 
						show: true,
						label: {
							show: true
						},
						innerRadius: 0.5
					}
				},
				legend: {
					show: false
				},
				radius: 1
				<? if ( count($colors) > 0 ) { ?> ,colors: ['<?=join("','",$colors)?>'] <? } ?>
			});
		});
		</script>
		<?
    }
	function getStyle() {
		return "height:460px;width:460px;float:left;";
	}
}