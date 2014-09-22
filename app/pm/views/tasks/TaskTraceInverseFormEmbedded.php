<?php

include_once SERVER_ROOT_PATH."pm/views/ui/ObjectTraceFormEmbedded.php";

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
}