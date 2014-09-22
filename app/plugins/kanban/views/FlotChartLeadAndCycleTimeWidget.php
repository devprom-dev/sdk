<?php

include_once SERVER_ROOT_PATH."core/views/charts/FlotChartBarWidget.php";
include "PolynomialRegression.php";

class FlotChartLeadAndCycleTimeWidget extends FlotChartBarWidget
{
	private $iterator;
	
	public function setIterator( $iterator )
	{
		$this->iterator = $iterator;
	}
	
    function getValues()
    {
    	$data = $this->getData();
		
    	$bar_data = array();
    	
    	$week_data = array();
    	
    	$y_values = array();
    	
    	$x_values = array();
    	
    	$poly_data = array();
    	
    	$urls = array();
    	
    	$index = 0;

		foreach ( $data as $key => $item )
		{
			$week_data[strftime('%Y,%W', $key)][] = $item['data'];
			
		    $bar_data[] = array( $index, 0 );
			
		    $bar_data[] = array( $index, max(round($item['data'],0), 0.5) );
		    
		    $y_values[] = $item['data'];
		    
		    $x_values[] = array( $index, 'I-'.$this->iterator->getId().' '.IteratorBase::wintoutf8($this->iterator->get('Caption')) );
		     
		    $urls[] = $this->iterator->getViewUrl();

		    $this->iterator->moveNext();
		    
		    $index++;
		}
		
		$index = 0;

		foreach ( $data as $key => $item )
		{
			$week_year = strftime('%Y,%W', $key);
			
			$average = round(array_sum($week_data[$week_year]) / (count($week_data[$week_year])), 1);
			
		    $poly_data[] = array( $index, $average );

		    $index++;
		}

		return array( $bar_data, $poly_data, $x_values, $urls );
    }

    public function draw( $chart_id )
    {
        list( $bar_data, $regression, $x_values, $urls ) = $this->getValues();
        
		?>
		<script type="text/javascript">
		$(function () {
			var barData = <?=JsonWrapper::encode($bar_data)?>;
			var average = <?=JsonWrapper::encode($regression)?>;
			var ticks = <?=JsonWrapper::encode($x_values)?>;
			var urls = <?=JsonWrapper::encode($urls)?>;

			$.plot($("#<?=$chart_id?>"), [{
				data: barData,
				bars: { 
					show: true, 
					barWidth: 0.6
				},
   				points: {
   					show: false
   				},
   				axisDescription: {
       				xaxis: "<?=text('kanban24')?>",
       				yaxis: "<?=text('kanban23')?>"
    			},
    			urls: urls
		    },{
				data: average,
				lines: { 
					show: true
				},
   				axisDescription: {
       				yaxis: "<?=text('kanban26')?>"
    			}
			}], 
			{
				colors: ["rgb(175,216,248)","rgb(0,0,248)"], 
				legend: {
					show: <?=($this->getShowLegend() ? 'true' : 'false')?>,
				},
				xaxis: {
					ticks: ticks,
					tickDecimals: 0,
					tickLength: 0,
					max: Math.max(ticks.length, 38),
					color: 'transparent',
					labelHeight: 0
				},
				yaxis: {
					tickLength: 0
				},
				grid: {
					hoverable: true,
					clickable: true,
					borderWidth: 0,
			        aboveData: true,
			        markings: [ { yaxis: { from: 0, to: 0 }, color: "#666" },
			                    { xaxis: { from: 0, to: 0 }, color: "#666" } ]
				}
			});
		});		
		</script>
		<?
    }
}