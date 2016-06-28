<?php
include "ActivityIterator.php";
include "predicates/ActivityIterationOnlyPredicate.php";
include "predicates/ActivityOtherIterationsPredicate.php";
include "predicates/ActivityRequestPredicate.php";
include "predicates/ActivityReportYearPredicate.php";
include "predicates/ActivityReportMonthPredicate.php";

class Activity extends Metaobject
{
 	function Activity() 
 	{
 		parent::__construct('pm_Activity');
		$this->defaultsort = 'RecordCreated ASC';

		$this->setAttributeCaption('Capacity', translate('Затрачено'));
		$this->setAttributeOrderNum('Capacity', 3);
		$this->setAttributeDescription('Capacity', text(2116));
 	}
 	
 	function createIterator() 
 	{
 		return new ActivityIterator( $this );
 	}
 	
 	function getDisplayName()
 	{
 		return translate('Списание времени');
 	}
 	
	function getByTask( $task_it )
	{
		return $this->getByRefArray( 
			array( 'Task' => $task_it->end() ? -1 : $task_it->getId() ) );
	}

	function updateTask( & $parms, $activity_id = 0 )
	{
		if ( $parms['Task'] < 1 ) return;
		
		$task = getFactory()->getObject('pm_Task');
		$task_it = $task->getRegistry()->Query(
				array(
						new FilterInPredicate( $parms['Task'] )
				)
		);
		
		if ( $task_it->getId() < 1 ) return;
		
		$parms['Iteration'] = $task_it->get('Release');
		
		$task->modify_parms($task_it->getId(), array (
			'LeftWork' => $parms['LeftWork'] != '' 
		        ? $parms['LeftWork'] : max(0, $task_it->get('LeftWork') - $parms['Capacity'])
		));
	}
	
	function add_parms( $parms )
	{
		$this->updateTask( $parms );

		return parent::add_parms( $parms );
	}

	function modify_parms( $activity_id, $parms )
	{
		// there is no way to update activity
		return 1;
	}
	
	function delete( $id, $record_version = ''  )
	{
	    global $model_factory;
	    
	    $activity_it = $this->getExact($id);
	    
	    $result = parent::delete( $id );
	    
		if ( $activity_it->get('Task') > 0 )
		{
		    $task_it = $activity_it->getRef('Task');
		    
			$sum_aggregate = new AggregateBase( 'Task', 'Capacity', 'SUM' );
		
			$activity = $model_factory->getObject('Activity');
			
			$activity->setVpdContext($activity_it);
			
			$activity->addFilter( new FilterAttributePredicate('Task', $task_it->getId()) );
			
        	$activity->addAggregate( $sum_aggregate );
        	
		    $it = $activity->getAggregated();
		
		    $left_work = max(0, $task_it->get('Planned') - $it->get($sum_aggregate->getAggregateAlias()) );

		    $task_it->object->modify_parms($task_it->getId(), array( 'LeftWork' => $left_work ));
		}
	    
	    return $result;
	}
}