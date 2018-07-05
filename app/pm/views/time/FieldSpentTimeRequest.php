<?php

include_once "FieldSpentTime.php";

class FieldSpentTimeRequest extends FieldSpentTime
{
	function getLeftWorkAttribute()
	{
	    return 'EstimationLeft';
	}

    function getAnchorField()
    {
        return 'Issue';
    }

 	function getObjectIt()
 	{
 	    $object_it = parent::getObjectIt();
 		if ( !is_object($object_it) ) {
 		    $object_it = getFactory()->getObject('pm_ChangeRequest')->getEmptyIterator();
 		}
 		return $object_it;
 	}
 	
 	function getObject()
 	{
 		$object_it = $this->getObjectIt();
 		
 		$activity = getFactory()->getObject('ActivityRequest');
 		$activity->addFilter( new FilterInPredicate($object_it->get('Spent') == '' ? '0' : $object_it->get('Spent') ) );

		return $activity;
 	}
}