<?php

include_once "FieldSpentTime.php";

class FieldSpentTimeRequest extends FieldSpentTime
{
	function getLeftWorkAttribute()
	{
	    return 'EstimationLeft';
	}
 	
 	function getObjectIt()
 	{
 	    global $model_factory;
 	    
 	    $object_it = parent::getObjectIt();
 		
 		if ( !is_object($object_it) )
 		{
 		    $object_it = $model_factory->getObject('pm_ChangeRequest')->getEmptyIterator(); 
 		}
 		
 		return $object_it;
 	}
 	
 	function getObject()
 	{
 		global $model_factory;
 		
 		$object_it = $this->getObjectIt();
 		
 		$activity = $model_factory->getObject('ActivityRequest');
 		
 		$activity->addFilter( new FilterInPredicate($object_it->get('Spent') == '' ? '0' : $object_it->get('Spent') ) );

		return $activity;
 	}
}