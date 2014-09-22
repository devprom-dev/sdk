<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ModuleCategoryBuilder.php";

class ModuleCategoryBuilderCode extends ModuleCategoryBuilder
{
	const AREA_UID = 'dev';
	
    public function build( ModuleCategoryRegistry & $object )
    {
    	$object->add( self::AREA_UID, translate('Разработка') );
    }
}