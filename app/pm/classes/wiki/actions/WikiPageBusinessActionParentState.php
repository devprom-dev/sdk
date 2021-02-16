<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once SERVER_ROOT_PATH . "pm/classes/workflow/actions/BusinessActionWorkflow.php";

class WikiPageBusinessActionParentState extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return '612263f5-bed2-41b8-8044-528f33a6660d';
 	}

    function getDisplayName() {
        return text(2945);
    }

	function apply( $object_it )
 	{
		if ( $object_it->get('ParentPage') == '' ) return true;

        $parentObject = getFactory()->getObject(get_class($object_it->object));
        $service = new WorkflowService($parentObject);

        try {
            $service->moveToState(
                $object_it, $object_it->getRef('ParentPage')->get('State'), '', array(), true
            );
        }
        catch( Exception $e ) {
            Logger::getLogger('System')->error($e->getMessage());
        }

 		return true;
 	}

 	function getObject()
 	{
 		return null;
 	}
}
