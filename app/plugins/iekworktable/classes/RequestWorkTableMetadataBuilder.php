<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";

include "predicates/WorkTableDepartmentPredicate.php";
include "predicates/WorkTableCustomerPredicate.php";
include "persisters/WorkTableDepartmentPersister.php";
include "persisters/WorkTableCustomerPersister.php";
include "persisters/WorkTableLinksPersister.php";

class RequestWorkTableMetadataBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_ChangeRequest' ) return;
    	
    	include_once SERVER_ROOT_PATH."pm/classes/issues/persisters/RequestReleaseDatesPersister.php";

		$object->addAttribute('ReleaseFinishDate', 'DATE', 'Плановый срок', true);
		
		$object->addPersister( new RequestReleaseDatesPersister() );
    	
		$object->addAttribute('Department', 'TEXT', 'Департамент', true, false);
		
		$object->addPersister( new WorkTableDepartmentPersister() );

		$object->addAttribute('Customer', 'TEXT', 'Заказчик', true, false);
		
		$object->addPersister( new WorkTableCustomerPersister() );
		
		$object->addAttribute('RecentComment', 'RICHTEXT', 'Комментарий', true);
		
		$object->addPersister( new CommentRecentPersister() );
		
		$object->addAttribute('LinkedIssues', 'TEXT', 'SD', true, false, '', 5);
		
		$object->addPersister( new WorkTableLinksPersister() );
    }
}
