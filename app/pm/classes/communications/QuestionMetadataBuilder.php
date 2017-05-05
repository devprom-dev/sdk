<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class QuestionMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Question' ) return;

        $metadata->setAttributeType('Content', 'WYSIWYG');
		$metadata->addAttribute('Owner', 'REF_UserId', translate('Ответственный'), true, true);
		$metadata->addAttribute('Attachment', 'REF_pm_AttachmentId', translate('Приложения'), true);
		$metadata->addAttribute('TraceRequests', 'REF_pm_ChangeRequestId', translate('Пожелания'), true);
   	}
}