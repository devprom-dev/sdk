<?php
include_once SERVER_ROOT_PATH."core/views/charts/FlotChartBarWidget.php";

class TaskPlanFactChartWidget extends FlotChartBarWidget
{
	private $iterator;
	
	public function setIterator( $iterator )
	{
		$this->iterator = $iterator;
	}
	
    function getValues()
    {
    	$data = $this->getData();
    	$positive_data = array();
		$negative_data = array();
    	$x_values = array();
        $labels = array();
    	$urls = array();

    	$index = 0;
    	$task = getFactory()->getObject('Task');
		foreach ( $data as $key => $item )
		{
			if ( $item['data'] >= 0 ) {
				$positive_data[] = array( $index, 0 );
				$positive_data[] = array( $index, round($item['data'],0) );
			}
			else {
				$negative_data[] = array( $index, 0 );
				$negative_data[] = array( $index, round($item['data'],0) );
			}

			list($finishDate, $taskId) = preg_split('/:/', $key);
			$taskIt = $task->getExact($taskId);

			$x_values[] = 'T-'.$taskIt->getId().' '.$taskIt->get('Caption');
            $labels[] = array($index, '');
		    $urls[] = $taskIt->getViewUrl();

		    $index++;
		}
		return array( $positive_data, $negative_data, $x_values, $urls, $labels );
    }

    public function draw( $chart_id )
    {
        list( $positive_data, $negative_data, $x_values, $urls, $labels ) = $this->getValues();

		?>
		<script type="text/javascript">
		var bar_labels = <?=JsonWrapper::encode($x_values)?>;
		$(function () {
            var ticks = <?=JsonWrapper::encode($labels)?>;
			$.plot($("#<?=$chart_id?>"), [{
				data: <?=JsonWrapper::encode($positive_data)?>,
				bars: {
					show: true,
					barWidth: 0.6
				},
				points: {
   					show: false
   				},
   				axisDescription: {
       				xaxis: "<?=translate('Задача')?>",
       				yaxis: "<?=text(2062)?>"
    			},
    			urls: <?=JsonWrapper::encode($urls)?>
		    }, {
				data: <?=JsonWrapper::encode($negative_data)?>,
				bars: {
					show: true,
					barWidth: 0.6
				},
				points: {
					show: false
				},
				axisDescription: {
					xaxis: "<?=translate('Задача')?>",
					yaxis: "<?=text(2062)?>"
				},
				urls: <?=JsonWrapper::encode($urls)?>
			}],
			{
				colors: ["rgb(0,248,0)", "rgb(248, 0, 0)"],
				legend: {
					show: <?=($this->getShowLegend() ? 'true' : 'false')?>,
				},
				xaxis: {
                    ticks: ticks,
                    tickDecimals: 0,
                    tickLength: 0,
                    max: Math.max(ticks.length, 38)
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