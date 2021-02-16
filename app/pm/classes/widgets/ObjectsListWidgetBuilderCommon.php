<?php
include_once SERVER_ROOT_PATH."pm/classes/widgets/ObjectsListWidgetBuilder.php";

class ObjectsListWidgetBuilderCommon extends ObjectsListWidgetBuilder
{
    function build( ObjectsListWidgetRegistry & $registry )
    {
    	$registry->addReport('Request', 'issues-trace', text(808));
    	$registry->addReport('Task', 'tasks-trace', translate('Задачи'));
        $registry->addModule('Feature', 'features-trace');
    }
}