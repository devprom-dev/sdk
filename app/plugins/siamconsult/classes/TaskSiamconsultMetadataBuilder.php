<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include "persisters/TaskSendAttachmentsPersister.php";

class TaskSiamconsultMetadataBuilder extends ObjectMetadataEntityBuilder
{
	private $session = null;
	
	function __construct( PMSession & $session ) {
		$this->session = $session;
	}
	
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Task' ) return;

		$metadata->addAttribute( 'AttachmentsToSend', 'REF_pm_AttachmentId', '', false);
        $metadata->addAttributeGroup('AttachmentsToSend', 'system');
        $metadata->addAttributeGroup('AttachmentsToSend', 'email-attachments');
		$metadata->addPersister( new TaskSendAttachmentsPersister(array('AttachmentsToSend')));
	}
}