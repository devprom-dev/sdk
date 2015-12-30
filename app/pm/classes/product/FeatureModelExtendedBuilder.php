<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

include "persisters/FeatureRequestPersister.php";

class FeatureModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_Function' ) return;

		$object->addAttribute('CaptionShort', 'VARCHAR', text(2105), false, false, '', 10);
		$object->addAttribute('Progress', '', translate('Прогресс'), false, false, '', 135);

		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->IsTimeTracking() ) {
			$object->addAttribute('Fact', 'FLOAT', translate('Затрачено'), false, false, '', 137);
		}

		$object->addAttribute('Request', 'REF_pm_ChangeRequestId', translate('Пожелания'), false, false, '', 140);
		$object->addAttributeGroup('Request', 'trace');
		$object->addPersister( new FeatureRequestPersister() );
 		
    	$module_it = getFactory()->getObject('Module')->getExact('dicts-featuretype');
	    $object->setAttributeDescription('Type', 
	    		str_replace('%1', '<a href="'.$module_it->get('Url').'">'.$module_it->getDisplayName().'</a>',text(1915))
			);
    }
}