<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "persisters/FeatureRequestPersister.php";
include "persisters/FeatureProgressPersister.php";

class FeatureModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_Function' ) return;

		$object->addAttribute('Progress', '', translate('Прогресс'), false, false, '', 135);

		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->IsTimeTracking() ) {
			$object->addAttribute('Fact', 'FLOAT', translate('Затрачено'), false, false, '', 137);
            $object->addAttributeGroup('Fact', 'hours');
		}
		if ( !getSession()->IsRDD() ) {
            $object->addPersister( new FeatureRequestPersister() );
            $object->addPersister( new FeatureProgressPersister() );
        }

    	$module_it = getFactory()->getObject('Module')->getExact('dicts-featuretype');
	    $object->setAttributeDescription('Type', 
	    		str_replace('%1', '<a href="'.$module_it->get('Url').'">'.$module_it->getDisplayName().'</a>',text(1915))
			);

        foreach ( array('Request') as $attribute ) {
            $object->addAttributeGroup($attribute, 'trace');
        }
    }
}