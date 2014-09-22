<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "persisters/StateBaseModelPersister.php";

class StateBaseModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof StateBase ) return;

    	$object->addAttribute('Attributes', 
			'REF_StateAttributeId', text(1800), true);

		$object->addAttribute('Actions', 
			'REF_StateActionId', translate('Системные действия'), true);
		
		$object->addPersister( new StateBaseModelPersister() );
    }
}