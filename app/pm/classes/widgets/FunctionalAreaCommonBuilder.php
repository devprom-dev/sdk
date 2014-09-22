<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuSettingsBuilder.php";

class FunctionalAreaCommonBuilder extends FunctionalAreaBuilder
{
    public function build( FunctionalAreaRegistry & $set )
    {
        $set->addArea( FUNC_AREA_FAVORITES );
         
        $set->addArea( FUNC_AREA_MANAGEMENT );

        $set->addArea( FunctionalAreaMenuSettingsBuilder::AREA_UID, array(), 'icon-wrench', 9999 );
    }
}