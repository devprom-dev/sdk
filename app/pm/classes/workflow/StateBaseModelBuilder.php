<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "persisters/StateBaseModelPersister.php";

class StateBaseModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof StateBase ) return;

    	$object->addAttribute('Attributes', 'REF_StateAttributeId', text(1800), true);
		$object->addAttribute('Actions', 'REF_StateActionId', translate('Системные действия'), true);
        $object->addAttribute('Transitions', 'REF_TransitionId', translate('Переходы'), true, false, str_replace('%1', $object->getPage(), text(2013)), 20);
		$object->addPersister( new StateBaseModelPersister() );
		
		$object->setAttributeCaption('OrderNum', text(1923));
		$object->setAttributeDescription('OrderNum', text(1924));

        $object->setAttributeCaption('IsTerminal', text(2212));
        $object->setAttributeDescription('IsTerminal', text(2106));
        $object->setAttributeDescription('RelatedColor', text(1835));
        $object->setAttributeType('IsTerminal', 'REF_StateCommonId');

        $object->setAttributeType('ReferenceName', 'varchar');
        $object->setAttributeVisible('ReferenceName', false);

        foreach( array('Description','OrderNum','ReferenceName','ExcludeLeadTime','SkipEmailNotification','IsNewArtifacts') as $attribute ) {
            $object->addAttributeGroup($attribute, 'additional');
        }
		foreach( array('QueueLength','RelatedColor') as $attribute ) {
			$object->addAttributeGroup($attribute, 'nonbulk');
		}
        foreach ( array( 'QueueLength' ) as $attribute ) {
            $object->addAttributeGroup($attribute, 'skip-total');
        }
        foreach( array('ObjectClass') as $attribute ) {
            $object->addAttributeGroup($attribute, 'system');
        }
    }
}