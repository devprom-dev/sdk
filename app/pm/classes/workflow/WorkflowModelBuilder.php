<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

include_once "persisters/TransitionAttributesPersister.php";
include_once "persisters/StateDurationPersister.php";
include_once "persisters/StateDetailsPersister.php";

class WorkflowModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof MetaobjectStatable ) return;
    	
 	    if ( $object->getStateClassName() == '' ) return;

		$object->addAttribute('LastTransition', 'REF_pm_TransitionId', text(1867), false);
		$object->addPersister( new TransitionAttributesPersister(array('LastTransition')) );

		$object->addAttribute('StateDuration', 'FLOAT', text(1364), false);
		$object->addAttribute('LeadTime', 'FLOAT', text(2067), false);
		$object->addPersister( new StateDurationPersister(array('LeadTime','StateDuration')) );
		$object->addPersister( new StateDetailsPersister() );

   	    $attributes = array( 'StateDuration', 'LeadTime' );
    	foreach ( $attributes as $attribute ) {
    		$object->addAttributeGroup($attribute, 'system');
    	}
		
    	$attributes = array( 'State', 'LastTransition', 'StateDuration', 'LeadTime' );
    	foreach ( $attributes as $attribute ) {
    		$object->addAttributeGroup($attribute, 'workflow');
    	}
    }
}