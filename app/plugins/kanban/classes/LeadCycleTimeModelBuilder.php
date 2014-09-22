<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "persisters/RequestLifecycleDurationPersister.php";

class LeadCycleTimeModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_ChangeRequest' ) return;
    	
        $object->setAttributeCaption('OrderNum', text('kanban24'));
        
	    $object->setAttributeCaption('LifecycleDuration', text('kanban23'));

	    $object->addPersister( new RequestLifecycleDurationPersister() );
    }
}