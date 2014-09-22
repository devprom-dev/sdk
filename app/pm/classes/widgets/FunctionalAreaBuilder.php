<?php

define( 'FUNC_AREA_FAVORITES', 'favs' );
define( 'FUNC_AREA_MANAGEMENT', 'mgmt' );

abstract class FunctionalAreaBuilder
{
    abstract public function build( FunctionalAreaRegistry & $set );
}