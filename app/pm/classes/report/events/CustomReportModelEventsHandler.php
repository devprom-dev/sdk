<?php

use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

include_once SERVER_ROOT_PATH."core/classes/model/events/SystemTriggersBase.php";

class CustomReportModelEventsHandler extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		$ref_name = $object_it->object->getEntityRefName();
		
		if ( $kind == TRIGGER_ACTION_DELETE ) return;
		
		if ( $ref_name != 'pm_CustomReport' ) return;
		
		if ( $object_it->get('IsHandAccess') != 'Y' ) return;
		
		$service = new WorkspaceService();
		
		$service->storeReportToWorkspace(
				array(
						'id' => $object_it->getId(),
						'type' => 'report'
				)
		);
	}
}
 