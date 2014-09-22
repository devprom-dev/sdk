<?php

class KanbanComulativeFlowSection extends InfoSection
{
 	var $object_it;
 	
 	function KanbanComulativeFlowSection( $object_it )
 	{
 		$this->object_it = $object_it;
 		
 		parent::InfoSection();
 	}

 	function getCaption()
 	{
 		return text('kanban12');
 	}

 	function drawBody()
 	{
 		global $model_factory, $project_it;
 		
 		if ( !is_a($this->object_it, 'IteratorBase') ) return;
 		
		$request = $model_factory->getObject('pm_ChangeRequest');

		$request->addFilter( new FilterInPredicate(join(',',$this->object_it->idsToArray())) ); 
		
		$aggregates = array(
			new AggregateBase( 'DayDate', '', '' ),
			new AggregateBase( 'State', 'State', 'COUNT' )
			);
			
		foreach ( $aggregates as $agg )
		{
			$request->addAggregate( $agg );
		}
		
		$sicne_last_month = getSession()->getLanguage()->getPhpDate( 
			strtotime('-1 month', strtotime(date('Y-m-j'))) );

		$predicates = array( 
			new FilterClusterPredicate($sicne_last_month) 
			);

		$flot = new FlotChartLineWidget();
		
		$flot->setLegend( false );
		$flot->showPoints( false );
		
		$report_it = $model_factory->getObject('PMReport')->getExact('issuesimplementationchart');
		
		$url = $report_it->getUrl();
		
		$chart_id = 'chart'.md5($url);

		echo '<div id="'.$chart_id.'" class="plot" url="'.$url.'" style="padding:3px;width:240px;height:150px;"></div>';
		
		$flot->setData( FlotChartDataSource::getData($request->getAggregatedHistory( $predicates ), $aggregates) );
		 
		$flot->draw( $chart_id );

		echo '<div style="clear:both;"></div>'; 
	}
}  