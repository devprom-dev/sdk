<?php

class FieldIssueState extends Field
{
 	var $object_it;
 	
 	function __construct( $object_it ) {
 		$this->object_it = $object_it;
 	}
 	
 	function draw(  $view = null  )
	{
		echo '<div class="input-block-level well well-text">';
			echo $view->render('pm/StateColumn.php', array (
					'color' => $this->object_it->get('StateColor'),
					'name' => $this->object_it->get('StateName'),
					'terminal' => $this->object_it->get('StateTerminal') == 'Y',
					'id' => $this->getId()
			));
			if ( $this->object_it->object->getAttributeType('Tasks') != '' ) {
				echo ' &nbsp; ';
				echo $view->render('pm/TasksIcons.php', array (
					'states' => $this->object_it->getRef('Tasks')->getStatesArray(),
					'random' => $this->object_it->getId()
				));
				echo ' &nbsp; ';
			}
			if ( $this->object_it->get('TransitionComment') != '' ) {
				echo ' &nbsp; ';
				drawMore($this->object_it, 'TransitionComment');
			}
		echo '</div>';
	}
}
