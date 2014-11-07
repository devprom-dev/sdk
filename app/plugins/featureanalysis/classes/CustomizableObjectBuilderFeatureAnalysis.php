<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CustomizableObjectBuilder.php";

class CustomizableObjectBuilderFeatureAnalysis extends CustomizableObjectBuilder
{
    public function build( CustomizableObjectRegistry & $set )
    {
        global $model_factory;
        
		$set->addObject( $model_factory->getObject('pm_Competitor') );
		
		$set->addObject( $model_factory->getObject('pm_FeatureAnalysis') );
    }
}