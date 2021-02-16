<?php

include "TaskTraceBaseIterator.php";
include "TaskInversedTraceBaseIterator.php";
include "predicates/TaskTraceObjectPredicate.php";
include "predicates/TaskTraceClassPredicate.php";
include "predicates/TaskTraceTaskPredicate.php";

class TaskTraceBase extends Metaobject
{
 	function __construct( ObjectRegistry $registry = null )
 	{
 		parent::Metaobject('pm_TaskTrace', $registry);

        foreach( array('ObjectId','ObjectClass','Task') as $attribute ) {
            $this->addAttributeGroup($attribute, 'alternative-key');
        }

 		$object_class = $this->getObjectClass();
 		if ( $object_class != '' ) {
     		$this->setAttributeType('ObjectId', 'REF_'.$object_class.'Id');
     		$this->setAttributeRequired('ObjectId', true);
 		}
        $this->setAttributeRequired('OrderNum', false);
 	}
 	
 	function createIterator() 
 	{
 		return new TaskTraceBaseIterator( $this );
 	}

	function getBaselineReference() {
		return 'Baseline';
	}

 	function getObjectClass()
 	{
 		return '';
 	}

	function getTaskIt( $object_it )
	{
		global $model_factory;
		
		$it = $this->getByRef('ObjectId', $object_it->getId());
		
		$task = $model_factory->getObject('pm_Task');
		
		if ( $it->count() < 1 ) return $task->getEmptyIterator(); 
		
		return $task->getInArray('pm_TaskId', $it->fieldToArray('Task'));
	}
	
	function getObjectIt( $task_it )
	{
		global $model_factory;
		
		$it = $this->getByRefArray(
			array( 'Task' => $task_it->getId() ) 
			);

		$object = $model_factory->getObject( $this->getObjectClass() );
		
		$object->defaultsort = ' RecordCreated DESC ';
		
		if ( $it->count() < 1 ) return $object->getEmptyIterator();
		
		return $object->getExact( $it->fieldToArray('ObjectId') );
	}
	
 	function resetFilters()
 	{
 		parent::resetFilters();
 		
 		if ( $this->getObjectClass() != '' )
 		{
		 	$this->addFilter( 
		 		new TaskTraceClassPredicate( $this->getObjectClass() ) 
		 		);
 		}
 	}
 	
	function getDefaultAttributeValue( $attr )
	{
 		switch ( $attr )
 		{
 			case 'ObjectClass':
 				return $this->getObjectClass();
 				
 			default:
 				return parent::getDefaultAttributeValue( $attr ); 
 		}
	}

	function add_parms( $parms )
	{
		$id = parent::add_parms( $parms );
		
		$task = getFactory()->getObject('pm_Task');
		$task_it = $task->getExact( $parms['Task'] );
		
		if ( $task_it->get('ChangeRequest') > 0 && $this->getObjectClass() != '' )
		{
			if ( $this->getObjectClass() == 'PreceedingTask' ) return $id;

			$req_trace = getFactory()->getObject('RequestTraceBase');
			$cnt = $req_trace->getByRefArrayCount(
				array( 'ChangeRequest' => $task_it->get('ChangeRequest'),
					   'ObjectId' => $parms['ObjectId'],
					   'ObjectClass' => $this->getObjectClass() )
				);

			if ( $cnt < 1 )
			{
				$req_trace->add_parms( array(
						'ChangeRequest' => $task_it->get('ChangeRequest'),
					  	'ObjectId' => $parms['ObjectId'],
					  	'ObjectClass' => $this->getObjectClass(),
						'Type' => REQUEST_TRACE_REQUEST
				));
			}
		}
		return $id;
	}
}