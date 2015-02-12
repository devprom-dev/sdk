<?php

include_once SERVER_ROOT_PATH."pm/views/ui/ObjectTraceFormEmbedded.php";
include_once SERVER_ROOT_PATH."pm/views/ui/FieldHierarchySelector.php";

class FunctionTraceInverseFormEmbedded extends ObjectTraceFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'Feature':
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
 			case 'Feature':
				$object = $this->getAttributeObject( $attr );

				$field = new FieldHierarchySelector( $object );
				$field->setTitle( $object->getDisplayName() ); 
				
				return $field;
				
 			default:
 				return parent::createField( $attr );
 		}
 	}
 	
   	function getTargetIt( $object_it )
 	{
 	    return $object_it->getRef('Feature');
 	}
}