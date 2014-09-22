<?php

include "FieldStateAction.php";
include "FieldStateAttribute.php";

class StateForm extends PMPageForm
{
 	function validateInputValues( $id, $action )
 	{
 		global $_REQUEST;
 		
 		$object = $this->getObject();
 		
 		$object->addFilter( new FilterBaseVPDPredicate() );
 		
 		$object_it = $object->getByRef('ReferenceName', $_REQUEST['ReferenceName']);
 		
 		if ( $object_it->count() > 0 && $object_it->getId() != $id )
 		{
 			return text(1121);
 		}
 		
 		return parent::validateInputValues( $id, $action );
 	}

 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch ( $attr_name )
 		{
 			case 'ObjectClass':
 				return false;
 				
 			default:
 				return parent::IsAttributeVisible( $attr_name );
 		}
 	}
 	
 	function getFieldDescription( $attr )
 	{
 		switch( $attr )
 		{
 			case 'Actions':
 				return text(1167);
 				
 			default:
 				return parent::getFieldDescription( $attr );
 		}
 	}
 	
 	function getRelatedEntity()
 	{
 		return getFactory()->getObject($_REQUEST['entity']);
 	}
 	
 	function createFieldObject( $attr_name ) 
	{
		switch( $attr_name )
		{
			case 'Actions':
				$field = new FieldStateAction($this->getObjectIt());
				
				$field->setObject( getFactory()->getObject($this->getRelatedEntity()->getObjectClass()) );
				
				return $field;
				
			case 'Attributes':

				return new FieldStateAttribute(is_object($this->getObjectIt()) ? $this->getObjectIt() : $this->getObject());
				
			default:
				return parent::createFieldObject( $attr_name );
		}
	}
}