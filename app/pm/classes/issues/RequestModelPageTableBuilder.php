<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include_once "persisters/RequestReleaseDatesPersister.php";
include_once "persisters/RequestIterationDatesPersister.php";
include_once "persisters/RequestPhotosPersister.php";
include_once "persisters/RequestEstimatesPersister.php";

class RequestModelPageTableBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_ChangeRequest' ) return;
    	
		$object->addPersister( new RequestPhotosPersister() );
		$object->addPersister( new WatchersPersister() );
		$object->addPersister( new AttachmentsPersister() );
		
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 	    
		$object->addPersister( new IssueLinkedIssuesPersister() );
		
    	if ( $methodology_it->HasReleases() )
    	{
    		$object->addAttribute('ReleaseStartDate', 'DATE', translate('Релиз').': '.translate('Начало'), false);
    		$object->addAttribute('ReleaseFinishDate', 'DATE', translate('Релиз').': '.translate('Окончание'), false);
    		$object->addAttribute('ReleaseEstimatedStart', 'DATE', translate('Релиз').': '.translate('Оценка начала'), false);
    		$object->addAttribute('ReleaseEstimatedFinish', 'DATE', translate('Релиз').': '.translate('Оценка окончания'), false);
    		$object->addPersister( new RequestReleaseDatesPersister() );

    		$attributes = array('ReleaseStartDate', 'ReleaseFinishDate', 'ReleaseEstimatedStart', 'ReleaseEstimatedFinish');
    	    foreach ( $attributes as $attribute )
        	{
        		$object->addAttributeGroup($attribute, 'dates');
        	}
    	}
    	
        if ( $methodology_it->HasPlanning() )
    	{
 	        $object->addAttribute('Iterations', 'REF_IterationId', translate('Итерация'), false);
 	        $object->addPersister( new RequestIterationsPersister() );
    		
 	        $object->addAttribute('IterationStartDate', 'DATE', translate('Итерация').': '.translate('Начало'), false);
    		$object->addAttribute('IterationFinishDate', 'DATE', translate('Итерация').': '.translate('Окончание'), false);
    		$object->addAttribute('IterationEstimatedStart', 'DATE', translate('Итерация').': '.translate('Оценка начала'), false);
    		$object->addAttribute('IterationEstimatedFinish', 'DATE', translate('Итерация').': '.translate('Оценка окончания'), false);
    		$object->addPersister( new RequestIterationDatesPersister() );

    		$attributes = array('IterationStartDate', 'IterationFinishDate', 'IterationEstimatedStart', 'IterationEstimatedFinish');
    	    foreach ( $attributes as $attribute )
        	{
        		$object->addAttributeGroup($attribute, 'dates');
        	}
    	}
    	
		$object->addAttribute('RecentComment', 'RICHTEXT', translate('Комментарии'), false);
		
		$comment = getFactory()->getObject('Comment');
		$object->addPersister( new CommentRecentPersister() );
		
 	    $object->addAttribute('TasksPlanned', 'FLOAT', text(1934), false);
 	    $object->addPersister( new RequestEstimatesPersister() );
		
       	$dates_attributes = array( 'Estimation', 'EstimationLeft', 'Fact', 'Spent', 'TasksPlanned' );
    	foreach ( $dates_attributes as $attribute )
    	{
    		$object->addAttributeGroup($attribute, 'time');
    	}

    	$dates_attributes = array( 'RecordModified', 'RecordCreated', 'StartDate', 'FinishDate', 'Deadlines', 'DeadlinesDate', 'DeliveryDate' );
    	foreach ( $dates_attributes as $attribute )
    	{
    		$object->addAttributeGroup($attribute, 'dates');
    	}
    }
}