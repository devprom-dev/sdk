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

		$object->addAttribute('LastTransition', 'REF_pm_TransitionId', text(1867), false);
		
		$object->addPersister( new TransitionAttributesPersister() );

		$object->addAttribute('StateDuration', 'INTEGER', text(1364), false);
		
		$object->addPersister( new StateDurationPersister() );

   	    $attributes = array( 'StateDuration' );
    	
    	foreach ( $attributes as $attribute )
    	{
    		$object->addAttributeGroup($attribute, 'system');
    	}
		
    	$attributes = array( 'State', 'TransitionComment', 'LastTransition', 'StateDuration' );
    	
    	foreach ( $attributes as $attribute )
    	{
    		$object->addAttributeGroup($attribute, 'workflow');
    	}
    }
}