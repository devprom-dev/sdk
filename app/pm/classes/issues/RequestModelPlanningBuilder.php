<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class RequestModelPlanningBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_ChangeRequest' ) return;
    	
		$object->addAttribute('Release', 'REF_IterationId', translate('Итерация'), true, false, '', 25);
		
		$object->setAttributeRequired( 'Release', true );
		
		$object->addPersister( new RequestPlanningPersister() );
    }
}