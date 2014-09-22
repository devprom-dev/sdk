<?php
 
class KanbanVersionList
{
 	static function drawCell( $object_it, $attr )
 	{
		switch( $attr )
		{
			case 'Progress':

			    if ( $object_it->object->getClassName() == 'pm_Version' )
				{
				    $project_it = $object_it->getRef('Project');
				    
				    $methodology_it = $project_it->getMethodologyIt();
				    
				    if ( $methodology_it->get('IsKanbanUsed') == 'Y' )
				    {
					    KanbanVersionList::drawCumulativeFlow( $object_it );
				    }
				}
				
				break;
				
			case 'Indexes':
				
			    echo '<br/><br/>';
				
				KanbanVersionList::drawIndexes( $object_it );
				
				break;
		}
		
		return false; // dont override drawCell of all others
 	}
 	
 	static function drawCumulativeFlow( $object_it )
 	{
 		global $model_factory, $project_it;
 		
		if ( $object_it->IsFuture() ) return;
 		
		$request = $model_factory->getObject('pm_ChangeRequest');
		$request->addFilter( 
			new FilterAttributePredicate('PlannedRelease', $object_it->getId()) );
		
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
		
		$url = $report_it->getUrl().'&release='.$object_it->getId();
		
		$chart_id = 'chart'.md5($url);

		echo '<div id="'.$chart_id.'" class="plot" url="'.$url.'" style="padding:3px;width:180px;height:100px;"></div>';
		
		$flot->setData( FlotChartDataSource::getData($request->getAggregatedHistory( $predicates ), $aggregates) );
		 
		$flot->draw( $chart_id );
		
		echo '<div style="clear:both;"></div>'; 
 	}
 	
 	static function drawIndexes( $object_it )
 	{
 		global $model_factory, $project_it;
 		
		$request = $model_factory->getObject('pm_ChangeRequest');
		$request->addFilter( 
			new FilterAttributePredicate('PlannedRelease', $object_it->getId()) );

		echo str_replace('%1', $request->getLifecycleDuration(), text('kanban13'));
 	}
}