<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once "persisters/RequestSpentTimePersister.php";

class RequestModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_ChangeRequest' ) return;
    	
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
    	if ( $methodology_it->HasMilestones() )
		{	
			$object->addAttribute('DeadlinesDate', 'DATE', translate('Сроки'), false);
		}
		
   		if ( $methodology_it->IsTimeTracking() )
		{
			$object->addAttribute( 'Spent', 'REF_ActivityRequestId', translate('Списание времени'), false );
			
		    $object->addPersister( new RequestSpentTimePersister() );
		}
		
		$object->addPersister( new RequestTasksPersister() );
    }
}