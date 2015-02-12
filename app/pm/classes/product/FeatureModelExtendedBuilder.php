<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

include "persisters/FeatureRequestPersister.php";

class FeatureModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_Function' ) return;
 	    
	    $object->addAttribute('Request', 'REF_pm_ChangeRequestId', translate('Пожелания'), false);
		$object->addPersister( new FeatureRequestPersister() );
 		
    	$object->addAttribute('Progress', '', translate('Прогресс'), false);
    	
    	$module_it = getFactory()->getObject('Module')->getExact('dicts-featuretype');
	    $object->setAttributeDescription('Type', 
	    		str_replace('%1', '<a href="'.$module_it->get('Url').'">'.$module_it->getDisplayName().'</a>',text(1915))
			);
    }
}