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
        $maxLabelLength = 0;
        foreach( $values as $value ) {
            if ( strlen($value['label']) > $maxLabelLength ) $maxLabelLength = strlen($value['label']);
            if ( $availableColors[$value['label']] == '' ) continue;
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
						    radius: 3/4,
							show: true,
                            color: 'white',
                            background: {
                                opacity: 0.5,
                                color: '#000'
                            },
                            formatter: function(label, series){
						        if ( label.length > 30 ) {
                                    return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+Math.round(series.percent)+'%</div>';
                                }
                                else {
                                    return '<div style="font-size:8pt;text-align:center;padding:2px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
                                }
                            },
						},
						innerRadius: 0.5
					}
				},
				legend: {
					show: <?=($maxLabelLength > 30 ? "true" : "false")?>
				},
				radius: 1
				<? if ( count($colors) > 0 ) { ?> ,colors: ['<?=join("','",$colors)?>'] <? } ?>
			});
		});
		</script>
		<?
    }
	function getStyle() {
		return "height:560px;width:560px;float:left;";
	}
}