<?php

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class TaskTypeStageIterator extends OrderedIterator
 {
 	function getDisplayName() 
 	{
 		$stage_it = $this->getRef('ProjectStage');
		return $stage_it->getDisplayName();
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 class TaskTypeStage extends Metaobject
 {
 	var $object_it;
 	
 	function TaskTypeStage( $task_type_it = null ) 
 	{
 		parent::Metaobject('pm_TaskTypeStage');
		$this->object_it = $task_type_it;
 	}
 	
 	function createIterator() 
 	{
 		return new TaskTypeStageIterator( $this );
 	}
 	
 	function getAll() 
 	{
 		if ( !is_null($this->object_it) )
 		{
	 		return $this->getByRefArray(
				array( 'TaskType' => $this->object_it ) );
 		}
 		else
 		{
	 		return parent::getAll();
 		}
 	}
 }

 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 class TaskTypeStageTaskTypePredicate extends FilterPredicate
 {
 	function _predicate( $filter )
 	{
 		return " AND t.TaskType = ".$filter;
 	}
 }

?>