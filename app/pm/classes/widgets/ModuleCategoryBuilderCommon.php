<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ModuleCategoryBuilder.php";

class ModuleCategoryBuilderCommon extends ModuleCategoryBuilder
{
    public function build( ModuleCategoryRegistry & $object )
    {
        $object->add( FUNC_AREA_FAVORITES, translate('Избранное') );
         
        $object->add( FUNC_AREA_MANAGEMENT, translate('Управление проектом') );

        $object->add( FunctionalAreaMenuSettingsBuilder::AREA_UID, translate('Настройки') );
    }
}