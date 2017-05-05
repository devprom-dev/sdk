<?php
include_once "persisters/TransitionAttributesPersister.php";
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class WorkflowMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( ! $metadata->getObject() instanceof MetaobjectStatable ) return;
 	    if ( $metadata->getObject()->getStateClassName() == '' ) return;

        $metadata->addAttribute('StateObject', 'INTEGER', '', false, true);
        $metadata->addAttribute('LastTransition', 'REF_pm_TransitionId', text(1867), false);
        $metadata->addAttribute('StateDuration', 'FLOAT', text(1364), false);
        $metadata->addAttribute('LeadTime', 'FLOAT', text(2067), false);
        $metadata->addPersister( new TransitionAttributesPersister() );

        foreach ( array( 'LifecycleDuration', 'StateObject', 'StateDuration', 'LeadTime' ) as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'system');
        }
        $attributes = array( 'State', 'LastTransition', 'StateDuration', 'LeadTime' );
        foreach ( $attributes as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'workflow');
        }
        foreach ( array( 'StateDuration', 'LeadTime' ) as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'skip-total');
            $metadata->addAttributeGroup($attribute, 'hours');
        }
    }
}