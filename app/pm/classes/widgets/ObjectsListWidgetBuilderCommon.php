<?php
include_once SERVER_ROOT_PATH."pm/classes/widgets/ObjectsListWidgetBuilder.php";

class ObjectsListWidgetBuilderCommon extends ObjectsListWidgetBuilder
{
    function build( ObjectsListWidgetRegistry & $registry )
    {
    	$registry->addReport('Request', 'allissues');
    	$registry->addReport('Task', 'currenttasks');
    }
}