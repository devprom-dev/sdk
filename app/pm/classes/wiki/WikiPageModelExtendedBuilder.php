<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/watchers/persisters/WatchersPersister.php";
include "persisters/WikiPageFeaturePersister.php";
include "persisters/WikiPageDetailsPersister.php";
include "persisters/WikiTagsPersister.php";
include "persisters/WikiPageAttachmentsPersister.php";
include "persisters/WikiPageWorkflowPersister.php";

class WikiPageModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'WikiPage' ) return;
    	
		$object->addPersister( new WikiPageDetailsPersister() );

        $object->addAttribute('Workflow', 'TEXT', text(2044), false);
        $object->addPersister( new WikiPageWorkflowPersister() );

		$object->addAttribute('Attachments', 'REF_WikiPageFileId', translate('Приложения'), false);
		$object->addPersister( new WikiPageAttachmentsPersister() );
		
		$object->addAttribute('Tags', 'REF_TagId', translate('Тэги'), false );
		$object->addPersister( new WikiTagsPersister() );
		
		$object->addAttribute('Watchers', 'REF_cms_UserId', translate('Наблюдатели'), false);
		$object->addPersister( new WatchersPersister() );

		$object->addAttribute('RecentComment', 'WYSIWYG', translate('Комментарии'), false);
		$object->addPersister( new CommentRecentPersister() );
    }
}