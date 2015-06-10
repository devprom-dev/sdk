<?php
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskViewModelBuilder.php";

class TaskViewModelCommonBuilder extends TaskViewModelBuilder
{
    public function build( Metaobject $object )
    {
   		$object->setAttributeDescription('Fact', 
			str_replace('%1', getFactory()->getObject('Module')->getExact('methodology')->get('Url'),
				str_replace('%2', getFactory()->getObject('PMReport')->getExact('activitiesreport')->get('Url'), 
					text(2009)))
 			);
    }
}