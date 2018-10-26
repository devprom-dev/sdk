<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/watchers/persisters/WatchersPersister.php";
include "persisters/WikiTagsPersister.php";
include "persisters/WikiPageAttachmentsPersister.php";
include "persisters/WikiPageWorkflowPersister.php";
include "persisters/WikiPageFeaturePersister.php";

class WikiPageModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( !$object instanceof WikiPage ) return;

        if ( $object->getStateClassName() != '' ) {
            $object->addAttribute('Workflow', 'TEXT', text(2044), false);
            $object->addAttributeGroup('Workflow', 'workflow');
            $object->addPersister(new WikiPageWorkflowPersister(array('Workflow')));
        }

		$object->addAttribute('Attachments', 'REF_WikiPageFileId', translate('Приложения'), false, false, '', 50);
		$object->addPersister( new WikiPageAttachmentsPersister(array('Attachments')) );
		
		$object->addAttribute('Tags', 'REF_TagId', translate('Тэги'), false );
		$object->addPersister( new WikiTagsPersister() );
		
		$object->addAttribute('Watchers', 'REF_WatcherId', translate('Наблюдатели'), false);
		$object->addPersister( new WatchersPersister(array('Watchers')) );

		$object->addAttribute('RecentComment', 'WYSIWYG', translate('Комментарии'), false);
		$object->addPersister( new CommentRecentPersister(array('RecentComment')) );

        if ( !$object instanceof ProjectPage ) {
            $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
            if( $methodology_it->HasFeatures() && getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Feature')) )
            {
                $visible = $object instanceof Requirement && $methodology_it->get('IsRequirements') == ReqManagementModeRegistry::RDD
                    || !$object instanceof Requirement;
                $object->addAttribute( 'Feature', 'REF_pm_FunctionId', translate('Функции'), $visible, false);
                $object->addPersister( new WikiPageFeaturePersister(array('Feature')) );
                $object->addAttributeGroup('Feature', 'trace');
                $object->addAttributeGroup('Feature', 'bulk');
            }
        }

        $object->addAttribute('UsedBy', 'REF_'.get_class($object).'Id', text(2154), true, false, text(2155), 205);
        $object->addAttributeGroup('UsedBy', 'trace');

        foreach( array('Tags', 'Attachments','Watchers',) as $attribute ) {
            $object->addAttributeGroup($attribute, 'skip-chart');
        }
		foreach( array('Tags', 'Attachments', 'Watchers', 'Author',) as $attribute ) {
			$object->addAttributeGroup($attribute, 'additional');
			$object->setAttributeRequired($attribute, false);
		}
        $object->addAttributeGroup('State', 'additional');
		$object->setAttributeOrderNum('Author', 400);
    }
}