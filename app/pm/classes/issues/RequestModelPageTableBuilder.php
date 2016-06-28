<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include_once "persisters/RequestReleaseDatesPersister.php";
include_once "persisters/RequestIterationDatesPersister.php";
include_once "persisters/RequestPhotosPersister.php";
include_once "persisters/RequestEstimatesPersister.php";
include_once "persisters/RequestDueDatesPersister.php";
include_once "persisters/RequestFeaturePersister.php";

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

		if ( $methodology_it->HasTasks() ) {
			$object->addAttribute('TasksPlanned', 'FLOAT', text(1934), false);
			$object->addPersister( new RequestEstimatesPersister(array('TasksPlanned')) );
		}

       	$dates_attributes = array( 'Estimation', 'EstimationLeft', 'Fact', 'Spent', 'TasksPlanned' );
    	foreach ( $dates_attributes as $attribute ) {
    		$object->addAttributeGroup($attribute, 'time');
    	}

		$object->addAttribute('DueDays', 'INTEGER', text(1937), false);
		$object->addAttribute('DueWeeks', 'REF_DeadlineSwimlaneId', text(1938), false);
		$object->addPersister( new RequestDueDatesPersister(array('DueDays','DueWeeks')) );
    	
    	$dates_attributes = array( 'RecordModified', 'RecordCreated', 'StartDate', 'FinishDate', 'Deadlines', 'DeadlinesDate', 'DeliveryDate', 'DueWeeks', 'DueDays' );
    	foreach ( $dates_attributes as $attribute )
    	{
    		$object->addAttributeGroup($attribute, 'dates');
    	}

		$object->addAttribute('Features', 'REF_FeatureId', text(2153), false);
		$object->addAttributeGroup('Features', 'trace');
		$object->addPersister( new RequestFeaturePersister(array()) );

		if ( class_exists('UserGroup') ) {
			$object->addAttribute('UserGroup', 'REF_UserGroupId', text('user.group.name'), false);
		}
	}
}