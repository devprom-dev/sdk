<?php

class FieldRequestState extends Field
{
 	var $object_it;
 	
 	function FieldRequestState( $object_it ) {
 		$this->object_it = $object_it;
 	}
 	
 	function draw( $view )
	{
		echo $view->render('pm/StateColumn.php', array (
				'color' => $this->object_it->get('StateColor'),
				'name' => $this->object_it->get('StateName'),
				'terminal' => $this->object_it->get('StateTerminal') == 'Y',
				'id' => $this->getId()
		)); 		
		echo '<br/>';

		if ( $this->object_it->object->getAttributeType('Tasks') != '' ) {
			echo $view->render('pm/TasksIcons.php', array (
				'states' => $this->object_it->getRef('Tasks')->getStatesArray()
			));
			echo ' &nbsp; ';
		}
		
        if ( $this->object_it->get('TransitionComment') != '' ) {
            drawMore($this->object_it, 'TransitionComment');
        }
	}
}
