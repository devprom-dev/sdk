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

    	$metadata->addAttribute('TraceRequests', 'REF_pm_ChangeRequestId', translate('���������'), true );
	    
	    $metadata->addPersister( new MilestoneRequestPersister() );
	    
	    $metadata->addAttribute('Overdue', 'INTEGER', translate('��������'), false );
	    
	    $metadata->addPersister( new MilestoneDatesPersister() );
	    
	    $metadata->addAttribute('RecentComment', 'TEXT', translate('�����������'), false );
	    
	    $metadata->addPersister( new CommentRecentPersister() );
    }
}