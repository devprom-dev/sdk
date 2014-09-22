<?php

include_once SERVER_ROOT_PATH."pm/views/ui/ObjectTraceFormEmbedded.php";

class RequestTraceInverseFormEmbedded extends ObjectTraceFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'ChangeRequest':
 				return true;
 			
 			default:
 				return false;
 		}
 	}
 	
 	function drawFieldTitle( $attr )
 	{
 	}
 	
 	function createField( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'ChangeRequest':
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
 	    return $object_it->getRef('ChangeRequest');
 	}
}