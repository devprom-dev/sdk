<?php
include_once SERVER_ROOT_PATH."pm/classes/widgets/ObjectsListWidgetBuilder.php";

class IntegrationObjectsListWidgetBuilder extends ObjectsListWidgetBuilder
{
    function build( ObjectsListWidgetRegistry & $registry )
    {
    	$registry->addModule('IntegrationLink', 'integration/list');
    }
}