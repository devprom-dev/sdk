<?php
include "ActivityIterator.php";
include "predicates/ActivityIterationOnlyPredicate.php";
include "predicates/ActivityOtherIterationsPredicate.php";
include "predicates/ActivityRequestPredicate.php";
include "predicates/ActivityReportYearPredicate.php";
include "predicates/ActivityReportMonthPredicate.php";
include "predicates/SpentTimeReportDatePredicate.php";

class Activity extends Metaobject
{
 	function __construct( ObjectRegistry $registry = null )
 	{
 		parent::__construct('pm_Activity', $registry);
		$this->defaultsort = 'RecordCreated ASC';

		$this->setAttributeCaption('Capacity', translate('Затрачено'));
		$this->setAttributeOrderNum('Capacity', 3);
		$this->setAttributeDescription('Capacity', text(2116));
        $this->addAttributeGroup('Capacity', 'daily-hours');

		foreach( array('Caption', 'Iteration') as $attribute ) {
		    $this->addAttributeGroup($attribute, 'system');
        }
        foreach( array('Issue', 'Task') as $attribute ) {
            $this->addAttributeGroup($attribute, 'trace');
        }

        $permission_attributes = array(
            'Capacity',
            'Participant',
            'Caption',
            'ReportDate'
        );
        foreach ( $permission_attributes as $attribute ) {
            $this->addAttributeGroup($attribute, 'permissions');
        }
    }
 	
 	function createIterator() {
 		return new ActivityIterator( $this );
 	}
 	
 	function getDisplayName() {
 		return translate('Списание времени');
 	}

 	function getPage() {
        return getSession()->getApplicationUrl($this).'worklog?';
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

		if ( !$task_it->IsFinished() ) {
            $task->modify_parms($task_it->getId(), array (
                'LeftWork' => $parms['LeftWork'] != ''
                    ? $parms['LeftWork'] : max(0, $task_it->get('LeftWork') - $parms['Capacity'])
            ));
        }
	}
	
	function add_parms( $parms )
	{
		$this->updateTask( $parms );

		return parent::add_parms( $parms );
	}

	function delete( $id, $record_version = ''  )
	{
	    $activity_it = $this->getExact($id);
	    
	    $result = parent::delete( $id );
	    
		if ( $activity_it->get('Task') > 0 )
		{
		    $task_it = $activity_it->getRef('Task');
		    
			$sum_aggregate = new AggregateBase( 'Task', 'Capacity', 'SUM' );
		
			$activity = getFactory()->getObject('Activity');
			
			$activity->setVpdContext($activity_it);
			
			$activity->addFilter( new FilterAttributePredicate('Task', $task_it->getId()) );
			
        	$activity->addAggregate( $sum_aggregate );
        	
		    $it = $activity->getAggregated();
		
		    $left_work = max(0, $task_it->get('Planned') - $it->get($sum_aggregate->getAggregateAlias()) );

		    if ( $task_it->get('LeftWork') != $left_work ) {
                $task_it->object->modify_parms($task_it->getId(), array( 'LeftWork' => $left_work ));
            }
		}
	    
	    return $result;
	}

	function IsDeletedCascade($object) {
        return $object instanceof Task || $object instanceof Request;
    }

    function getDefaultAttributeValue($name)
    {
        switch( $name ) {
            case 'ReportDate':
                return date('Y-m-j');
        }
        return parent::getDefaultAttributeValue($name);
    }
}