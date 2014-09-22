<?php

include_once SERVER_ROOT_PATH."pm/classes/permissions/AccessRightEntitySetBuilder.php";

class AccessRightEntitySetCodeBuilder extends AccessRightEntitySetBuilder
{
    public function build( CommonAccessRight $set )
    {
        global $model_factory;
        
		$set->addObject($model_factory->getObject('pm_Subversion'));
    }
}