<?php

include_once "FieldSpentTime.php";

class FieldSpentTimeTask extends FieldSpentTime
{
	function getLeftWorkAttribute()
	{
	    return 'LeftWork';
	}
 	
 	function getObjectIt()
 	{
 	    global $model_factory;
 	    
 	 	$object_it = parent::getObjectIt();
 		
 	 	if ( !is_object($object_it) )
 		{
 		    $object_it = $model_factory->getObject('pm_Task')->getEmptyIterator(); 
 		}
 		
 		return $object_it;
 	}
 	
 	function getObject()
 	{
 		global $model_factory;
 		
 		$object_it = $this->getObjectIt();
 		
 		$activity = $model_factory->getObject('ActivityTask');
 		
 		$activity->addFilter( new FilterInPredicate($object_it->get('Spent') == '' ? '0' : $object_it->get('Spent') ) );
 		
		return $activity;
 	}
}
