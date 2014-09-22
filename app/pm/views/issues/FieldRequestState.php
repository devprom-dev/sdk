<?php

class FieldRequestState extends Field
{
 	var $object_it;
 	
 	function FieldRequestState( $object_it )
 	{
 		$this->object_it = $object_it;
 	}
 	
 	function draw( $view )
	{
		$states = $this->object_it->getStateExact();
		
		echo '<span class="label label-warning" id="'.$this->getId().'">'.$states[0].'</span> ';
		
		if ( $this->object_it->object->getAttributeType('Tasks') != '' )
		{
			echo $view->render('pm/TasksIcons.php', array (
					'states' => $this->object_it->getRef('Tasks')->getStatesArray()
			));
		}
		
        if ( $this->object_it->get('TransitionComment') != '' )
        {
            drawMore($this->object_it, 'TransitionComment');		
        }
	}
}
