<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/StyleSheetBuilder.php";

class ProductTourCSSBuilder extends StyleSheetBuilder
{
    public function build( StyleSheetRegistry & $object )
    {
    	$object->addScriptFile(SERVER_ROOT_PATH.'/plugins/producttour/resources/css/bootstrap-tour.min.css');
    	$object->addScriptFile(SERVER_ROOT_PATH.'/plugins/producttour/resources/css/extended.css');
    }
}