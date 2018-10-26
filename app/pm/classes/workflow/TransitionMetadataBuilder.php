<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include "persisters/TransitionDetailsPersister.php";

class TransitionMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof Transition ) return;

		$metadata->addAttribute('Attributes', 
			'REF_TransitionAttributeId', text(1800), true);

		$metadata->addAttribute('ProjectRoles', 
			'REF_TransitionRoleId', translate('Проектные роли'), false);

		$metadata->addAttribute('Predicates', 
			'REF_TransitionPredicateId', translate('Предусловия'), true);

        $metadata->addAttribute('Actions', 'REF_TransitionActionId', translate('Системные действия'), true);

		$metadata->addAttribute('ResetFields',
			'REF_TransitionResetFieldId', translate('Очищаемые поля'), true);

		$metadata->addPersister(new TransitionDetailsPersister());

        foreach( array('Description','OrderNum') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'additional');
            $metadata->setAttributeRequired($attribute, false);
        }

        $metadata->setAttributeRequired('IsReasonRequired', true);
        $metadata->setAttributeDefault('IsReasonRequired', TransitionReasonTypeRegistry::None);
    }
}