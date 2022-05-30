<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include "persisters/MilestoneDatesPersister.php";
include "persisters/MilestoneRequestPersister.php";

class MilestoneMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Milestone' ) return;

        $metadata->setAttributeOrderNum('Caption', 1);
        $metadata->setAttributeVisible('CompleteResult', false);

    	$metadata->addAttribute('TraceRequests', 'REF_pm_ChangeRequestId', text(808), true );
	    $metadata->addPersister( new MilestoneRequestPersister() );
	    
	    $metadata->addAttribute('Overdue', 'INTEGER', translate('Смещение'), false );
	    $metadata->addPersister( new MilestoneDatesPersister() );
	    
	    $metadata->addAttribute('RecentComment', 'WYSIWYG', translate('Комментарии'), true );
	    $metadata->addPersister( new CommentRecentPersister() );

        foreach( array('TraceRequests','MilestoneDate','Description') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'tooltip');
        }

        foreach( array('ReasonToChangeDate','Passed') as $attribute ) {
            $metadata->setAttributeVisible($attribute, false);
            $metadata->addAttributeGroup($attribute, 'system');
        }

        foreach ( array('MilestoneDate','Caption','Description') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'permissions');
        }

        foreach ( array('TraceRequests') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'trace');
        }
    }
}