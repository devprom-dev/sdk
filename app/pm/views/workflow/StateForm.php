<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/persisters/StateTransitionsPersister.php";
include "FieldStateAction.php";
include "FieldStateAttribute.php";
include "FieldStateTransitions.php";

class StateForm extends PMPageForm
{
	function extendModel()
	{
		parent::extendModel();

		$this->getObject()->addAttribute('Transitions', 'INTEGER', text(2016), true, false, 
				str_replace('%1', $this->getObject()->getPage(), text(2013)), 25);
		$this->getObject()->addPersister( new StateTransitionsPersister() );		

		$this->getObject()->setAttributeOrderNum('ReferenceName', 998);
		$this->getObject()->setAttributeVisible('ReferenceName', $this->getMode() != 'new');
		$this->getObject()->setAttributeRequired('ReferenceName', false);
		
		$this->getObject()->setAttributeType('Description', 'TEXT');
		$this->getObject()->setAttributeOrderNum('Description', 999);
		
		$this->getObject()->setAttributeOrderNum('OrderNum', 997);
	}
	
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
				
			case 'Transitions':
				return new FieldStateTransitions(is_object($this->getObjectIt()) ? $this->getObjectIt() : $this->getObject());

            case 'IsTerminal':
                $field = new FieldDictionary( new StateCommon() );
                $field->setNullOption(false);
                return $field;

			default:
				return parent::createFieldObject( $attr_name );
		}
	}
}