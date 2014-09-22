<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

include "persisters/FeatureRequestPersister.php";
include "persisters/FeatureDatesPersister.php";

class FeatureModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_Function' ) return;
    	
 	    $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 	    
   		$object->addAttribute('Request', 'REF_pm_ChangeRequestId', translate('���������'), false);
 		
		$object->addPersister( new FeatureRequestPersister() );
 		
    	$object->addAttribute('Progress', '', translate('��������'), false);
		
		$object->addAttribute('Estimation', 'FLOAT', translate('������������'), false);

		$object->addAttribute('Workload', 'FLOAT', translate('��������'), false);

 		$object->addAttribute('StartDate', 'DATE', translate('���� ������'), false, false, text(1837));
 		
		if ( is_object($methodology_it) && $methodology_it->HasReleases() )
 		{
 		    $object->addAttribute('DeliveryDate', 'DATE', translate('���� ����������'), false, false, text(1838));
 		}

	    $object->addPersister( new FeatureDatesPersister() );
    }
}