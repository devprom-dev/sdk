<?php
include_once SERVER_ROOT_PATH."pm/classes/widgets/ObjectsListWidgetBuilder.php";

class ObjectsListWidgetBuilderCode extends ObjectsListWidgetBuilder
{
    function build( ObjectsListWidgetRegistry & $registry )
    {
    	$registry->addModule('SubversionRevision', 'sourcecontrol/revision');
    }
}