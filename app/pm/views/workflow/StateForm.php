<?php
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
        if ( $stateObject instanceof Requirement || $stateObject instanceof TestScenario ) {
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
 				return preg_replace('/%1/',
                    getFactory()->getObject('Module')
                        ->getExact('autoactions')->getUrl(), text(1167));

            case 'Attributes':
                return sprintf(text(3203),
                    getFactory()->getObject('Module')
                        ->getExact('dicts-stateattribute')->getUrl());

 			default:
 				return parent::getFieldDescription( $attr );
 		}
 	}
 	
 	function getRelatedEntity() {
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
				return new FieldStateAttribute(
				    is_object($this->getObjectIt())
                        ? $this->getObjectIt()
                        : $this->getObject(),
                    is_object($this->getObjectIt())
                        ? $this->getObjectIt()->getObject()
                        : getFactory()->getObject($this->getRelatedEntity()->getObjectClass())
                );
				
			case 'Transitions':
				return new FieldStateTransitions(
				    is_object($this->getObjectIt()) ? $this->getObjectIt() : $this->getObject());

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

	function persist()
    {
        if ( !parent::persist() ) return false;

        $id = $this->getObjectIt()->getId();
        if ( $id == '' ) return true;
        $parms = $_REQUEST;

        $transition = getFactory()->getObject('Transition');
        $transition_it = $transition->getRegistry()->Query(
            array ( new FilterAttributePredicate('SourceState', $id) )
        );

        $state_it = $this->getObject()->getRegistry()->Query(
            array (
                new FilterBaseVpdPredicate()
            )
        );
        while( !$state_it->end() )
        {
            if ( strtolower($parms['ForwardRequired'.$state_it->getId()]) == 'on' )
            {
                $transition_it->moveTo('TargetState', $state_it->getId());
                if ( $transition_it->getId() == '' ) {
                    $transition->add_parms(
                        array (
                            'Caption' => $state_it->getHtmlDecoded('Caption'),
                            'SourceState' => $id,
                            'TargetState' => $state_it->getId()
                        )
                    );
                }
            }
            else
            {
                $transition_it->moveTo('TargetState', $state_it->getId());
                if ( $transition_it->getId() != '' ) {
                    $transition->delete($transition_it->getId());
                }
            }
            $state_it->moveNext();
        }

        $transition_it = $transition->getRegistry()->Query(
            array ( new FilterAttributePredicate('TargetState', $id) )
        );
        $state_it->moveFirst();
        while( !$state_it->end() )
        {
            if ( strtolower($parms['BackwardRequired'.$state_it->getId()]) == 'on' )
            {
                $transition_it->moveTo('SourceState', $state_it->getId());
                if ( $transition_it->getId() == '' ) {
                    $transition->add_parms(
                        array (
                            'Caption' => $parms['Caption'],
                            'SourceState' => $state_it->getId(),
                            'TargetState' => $id
                        )
                    );
                }
            }
            else
            {
                $transition_it->moveTo('SourceState', $state_it->getId());
                if ( $transition_it->getId() != '' ) {
                    $transition->delete($transition_it->getId());
                }
            }
            $state_it->moveNext();
        }

        return true;
    }
}