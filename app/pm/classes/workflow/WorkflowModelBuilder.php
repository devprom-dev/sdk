<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

include_once "persisters/TransitionAttributesPersister.php";
include_once "persisters/StateDurationPersister.php";

class WorkflowModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof MetaobjectStatable ) return;
    	
 	    if ( $object->getStateClassName() == '' ) return;

		$object->addAttribute('TransitionComment', 'LARGETEXT', text(1197), false);

		$object->addAttribute('Transition', 'REF_pm_TransitionId', translate('Переход'), false);
		
		$object->addPersister( new TransitionAttributesPersister() );

		$object->addAttribute('StateDuration', 'INTEGER', text(1364), false);
		
		$object->addPersister( new StateDurationPersister() );

		$object->addAttribute('StateObject', 'INTEGER', '', false, true);
		
   	    $attributes = array( 'Transition', 'StateObject', 'StateDuration' );
    	
    	foreach ( $attributes as $attribute )
    	{
    		$object->addAttributeGroup($attribute, 'system');
    	}
		
    	$attributes = array( 'TransitionComment', 'Transition', 'StateDuration' );
    	
    	foreach ( $attributes as $attribute )
    	{
    		$object->addAttributeGroup($attribute, 'workflow');
    	}
    }
}