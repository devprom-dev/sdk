<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaBuilder.php";

class FunctionalAreaMyProjectsBuilder extends FunctionalAreaBuilder
{
    public function build( FunctionalAreaRegistry & $set )
    {
        $set->addArea( FUNC_AREA_FAVORITES );
    }
}