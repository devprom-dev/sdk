<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ModuleCategoryBuilder.php";

class ModuleCategoryBuilderFileServer extends ModuleCategoryBuilder
{
	const AREA_UID = 'cicd';
	
    public function build( ModuleCategoryRegistry & $object )
    {
    	$object->add( self::AREA_UID, translate('Развертывание') );
    }
}