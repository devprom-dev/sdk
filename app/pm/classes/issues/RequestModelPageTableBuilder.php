<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include_once "persisters/RequestReleaseDatesPersister.php";
include_once "persisters/RequestPhotosPersister.php";
include_once "persisters/RequestDueDatesPersister.php";
include "persisters/RequestProgressPersister.php";

class RequestModelPageTableBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
        if ( !$object instanceof Request) return;

		$object->addPersister( new RequestPhotosPersister() );
		$object->addPersister( new AttachmentsPersister() );
		$object->addPersister( new CommentRecentPersister(array('RecentComment')) );

    	$dates_attributes = array( 'RecordModified', 'RecordCreated', 'StartDate', 'FinishDate', 'DeliveryDate', 'DueWeeks');
    	foreach ( $dates_attributes as $attribute ) {
    		$object->addAttributeGroup($attribute, 'dates');
    	}

		if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED ) {
			$object->addAttribute('UserGroup', 'REF_UserGroupId', text('user.group.name'), false);
		}

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->HasTasks() ) {
            $object->addAttribute('HoursProgress', 'INTEGER', translate('Прогресс'), false);
            $object->addAttributeGroup('HoursProgress', 'percentage');
            $object->addAttributeGroup('HoursProgress', 'skip-total');
            $object->addAttribute('TaskTypeProgress', 'VARCHAR', text(2842), false);
            $object->addPersister( new RequestProgressPersister() );
        }
	}
}