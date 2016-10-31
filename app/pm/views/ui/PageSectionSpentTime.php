<?php

class PageSectionSpentTime extends InfoSection
{
 	var $object_it;
 	
 	function __construct( $object_it )
 	{
 		$this->object_it = $object_it;
 		
 		parent::__construct();
 	}
 	
 	function getCaption()
 	{
 		return translate('Затраченное время');
 	}
 	
 	function getObjectIt()
 	{
 		return $this->object_it;
 	}

 	function getRenderParms()
	{
		$activities = array();
		
		$activity_it = $this->object_it->getRef('Spent');
		while( !$activity_it->end() )
		{
			$activities[] = array (
					'date' => $activity_it->getDateFormat('ReportDate'),
					'capacity' => $activity_it->get('Capacity'),
					'user' => $activity_it->getRef('Participant')->getDisplayName(),
					'description' => $activity_it->getHtml('Description'),
					'actions' => $this->getItemActions($activity_it) 
			);
					 
			$activity_it->moveNext();
		}
		
		return array_merge( parent::getRenderParms(), array (
			'section' => $this,
			'spent_hours' => $this->object_it->get('Fact'),
			'activities' => $activities
		));
	}
	
	function getItemActions( $object_it )
	{
		$actions = array();
		
		$method = new DeleteObjectWebMethod($object_it);
			
		if ( $method->hasAccess() )
		{
		    $actions[] = array(
			    'name' => $method->getCaption(), 'url' => $method->getJSCall() 
		    );
		}
		
		return $actions;
	}
 	
 	function getTemplate()
	{
		return 'pm/PageSectionSpentTime.php';
	}
}  
