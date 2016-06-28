<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include "persisters/TransitionDetailsPersister.php";

class TransitionMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof Transition ) return;

		$metadata->addAttribute('Attributes', 
			'REF_TransitionAttributeId', translate('Обязательные поля'), true);

		$metadata->addAttribute('ProjectRoles', 
			'REF_TransitionRoleId', translate('Проектные роли'), false);

		$metadata->addAttribute('Predicates', 
			'REF_TransitionPredicateId', translate('Предусловия'), true);

		$metadata->addAttribute('ResetFields', 
			'REF_TransitionResetFieldId', translate('Очищаемые поля'), true);

		$metadata->addPersister(new TransitionDetailsPersister());
    }
}