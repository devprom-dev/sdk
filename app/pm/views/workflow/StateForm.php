<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/persisters/StateTransitionsPersister.php";
include "FieldStateAction.php";
include "FieldStateAttribute.php";
include "FieldStateTransitions.php";
include "FieldStateTaskTypes.php";

class StateForm extends PMPageForm
{
	function extendModel()
	{
		parent::extendModel();

		$object = $this->getObject();

		$object->addPersister( new StateTransitionsPersister() );
		$object->setAttributeOrderNum('ReferenceName', 998);
		$object->setAttributeVisible('ReferenceName', $this->getMode() != 'new');

		$object->setAttributeType('Description', 'TEXT');
		$object->setAttributeOrderNum('Description', 999);
		$object->setAttributeOrderNum('OrderNum', 997);

		$stateObject = getFactory()->getObject($object->getObjectClass());
        if ( $stateObject->getEntityRefName() == 'pm_ChangeRequest' ) {
            $object->addAttribute('TaskTypes', 'REF_TaskTypeId', translate('Задачи'), true, false, text(2273), 40);
            $object->setAttributeVisible('ArtifactsType', true);
            $object->addAttributeGroup('ArtifactsType', 'additional');
        }
        if ( $stateObject instanceof Requirement) {
            $object->setAttributeVisible('ArtifactsType', true);
            $object->addAttributeGroup('ArtifactsType', 'additional');
        }
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
 				return preg_replace('/%1/', getFactory()->getObject('Module')->getExact('autoactions')->getUrl(), text(1167));
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

            case 'TaskTypes':
                return new FieldStateTaskTypes($this->getObjectIt());

            case 'IsTerminal':
                $field = new FieldDictionary( new StateCommon() );
                $field->setNullOption(false);
                return $field;

            case 'ArtifactsType':
                return new FieldDictionary( new StateArtifactsType() );

			default:
				return parent::createFieldObject( $attr_name );
		}
	}
}