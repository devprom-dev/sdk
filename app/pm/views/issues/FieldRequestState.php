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
		
		echo $view->render('pm/StateColumn.php', array (
				'color' => $this->object_it->get('StateColor'),
				'name' => $states[0],
				'terminal' => $this->object_it->get('StateTerminal') == 'Y',
				'id' => $this->getId()
		)); 		
		
		if ( $this->object_it->object->getAttributeType('Tasks') != '' )
		{
			echo $view->render('pm/TasksIcons.php', array (
					'states' => $this->object_it->getRef('Tasks')->getStatesArray()
			));
		}
		
        if ( $this->object_it->get('TransitionComment') != '' )
        {
        	echo ' &nbsp; ';
            drawMore($this->object_it, 'TransitionComment');		
        }
	}
}
