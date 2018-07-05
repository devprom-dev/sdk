<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include_once "persisters/RequestReleaseDatesPersister.php";
include_once "persisters/RequestPhotosPersister.php";
include_once "persisters/RequestDueDatesPersister.php";
include_once "persisters/RequestFeaturePersister.php";
include_once "persisters/RequestColorsPersister.php";

class RequestModelPageTableBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_ChangeRequest' ) return;
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		$object->addPersister( new RequestPhotosPersister() );
		$object->addPersister( new AttachmentsPersister(array('Attachment')) );

		$object->addAttribute('RecentComment', 'WYSIWYG', translate('Комментарии'), false);
		
		$comment = getFactory()->getObject('Comment');
		$object->addPersister( new CommentRecentPersister(array('RecentComment')) );

    	$dates_attributes = array( 'RecordModified', 'RecordCreated', 'StartDate', 'FinishDate', 'DeliveryDate', 'DueWeeks');
    	foreach ( $dates_attributes as $attribute ) {
    		$object->addAttributeGroup($attribute, 'dates');
    	}

		$object->addAttribute('Features', 'REF_FeatureId', translate('Функции'), false);
		$object->addAttributeGroup('Features', 'trace');
		$object->addPersister( new RequestFeaturePersister(array()) );

		if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED ) {
			$object->addAttribute('UserGroup', 'REF_UserGroupId', text('user.group.name'), false);
		}
		$object->addPersister( new RequestColorsPersister() );
	}
}