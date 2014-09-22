<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ModuleCategoryBuilder.php";

class ModuleCategoryBuilderCommon extends ModuleCategoryBuilder
{
    public function build( ModuleCategoryRegistry & $object )
    {
        $object->add( FUNC_AREA_FAVORITES, translate('���������') );
         
        $object->add( FUNC_AREA_MANAGEMENT, translate('���������� ��������') );

        $object->add( FunctionalAreaMenuSettingsBuilder::AREA_UID, translate('���������') );
    }
}