<?php
include_once "persisters/TransitionAttributesPersister.php";
include_once "persisters/TransitionCommentPersister.php";
include_once "persisters/StateDurationPersister.php";
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

        foreach ( array( 'LifecycleDuration', 'StateObject' ) as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'system');
        }
        foreach ( array( 'StateDuration', 'LeadTime' ) as $attribute ) {
            $metadata->setAttributeEditable($attribute, false);
        }
        $attributes = array( 'State', 'LastTransition', 'StateDuration', 'LeadTime' );
        foreach ( $attributes as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'workflow');
        }
        $attributes = array( 'State' );
        foreach ( $attributes as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'permissions');
        }
        foreach ( array( 'StateDuration', 'LeadTime' ) as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'skip-total');
            $metadata->addAttributeGroup($attribute, 'astronomic-time');
        }
        foreach ( array( 'StateDuration' ) as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'tooltip');
        }
        $metadata->addPersister(new TransitionCommentPersister());
        $metadata->addPersister(new StateDurationPersister());
    }
}