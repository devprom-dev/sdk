<?php

include_once SERVER_ROOT_PATH."pm/views/ui/ObjectTraceFormEmbedded.php";
include_once SERVER_ROOT_PATH."pm/views/tasks/TaskForm.php";

class TaskTraceInverseFormEmbedded extends ObjectTraceFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'Task':
 				return true;
 			
 			default:
 				return false;
 		}
 	}
 	
 	function drawFieldTitle( $attr )
 	{
 	}
 	
	function getFieldDescription( $attr )
	{
		return $this->getObject()->getAttributeDescription($attr);
	}
 	
 	function createField( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Task':
 			    $object = $this->getAttributeObject( $attr );

				$field = new FieldAutoCompleteObject( $object );
				$field->setTitle( $object->getDisplayName() ); 
				return $field;
				
 			default:
 			    
 				return parent::createField( $attr );
 		}
 	}
 	
  	function getTargetIt( $object_it )
 	{
 	    return $object_it->getRef('Task');
 	}
 	
 	function getSaveCallback()
 	{
 		if ( $this->getObjectIt()->object instanceof TestExecution )
 		{
 			return 'reloadPage';
 		}
 		return parent::getSaveCallback();
 	}

 	function getActions( $object_it, $item )
 	{
 		$task_it = $this->getTargetIt( $object_it );
 		$form = new TaskForm($task_it->object);
 		$form->show($task_it);
 		$form->getRenderParms();
 		return $form->getActions();
 	}
}