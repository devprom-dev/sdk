<?php
include_once SERVER_ROOT_PATH."pm/views/ui/ObjectTraceFormEmbedded.php";
include_once SERVER_ROOT_PATH."pm/views/ui/FieldHierarchySelector.php";

class ComponentTraceInverseFormEmbedded extends ObjectTraceFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute ) {
 			case 'Component':
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
 		switch ( $attr ) {
 			case 'Component':
				$object = $this->getAttributeObject( $attr );
				$field = new FieldHierarchySelector( $object );
				$field->setTitle( $object->getDisplayName() );
                $field->setMultiselect();
    			return $field;
 			default:
 				return parent::createField( $attr );
 		}
 	}
 	
   	function getTargetIt( $object_it ) {
 	    return $object_it->getRef('Component');
 	}

	function getListItemsAttribute() {
		return 'Component';
	}
}