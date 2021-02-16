<?php
use Devprom\ProjectBundle\Service\Task\TaskConvertToIssueService;
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class TaskConvertToIssueWebMethod extends WebMethod
{
    private $task_it = null;

 	function __construct( $task_it = null ) {
 	    $this->task_it = $task_it;
 		parent::__construct();
 	}
 	
 	function hasAccess() {
 		return getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Request'));
 	}

    function getMethodName() {
        return 'Method:'.get_class($this);
    }

 	function getCaption() {
 		return text(2228);
 	}

	function url( $taskId ) {
	    return $this->getJSCall(
	        array (
	            'Task' => $taskId
            )
        );
    }
	
 	function execute_request()
 	{
 	    if ( is_object($this->task_it) ) $_REQUEST['Task'] = join(',',$this->task_it->idsToArray());

		if ( $_REQUEST['Task'] == '' ) throw new Exception('Unknown task given');
		if ( !$this->hasAccess() ) throw new Exception('There is no access to complete the operation');

        $task_it = getFactory()->getObject('Task')->getExact(TextUtils::parseIds($_REQUEST['Task']));
        if ( $task_it->getId() == '' ) throw new Exception('Unknown task given');

        $targetClass = $_REQUEST['targetClass'];
        if ( $targetClass != '' ) {
            $targetClass = getFactory()->getClass($targetClass);
            if ( !class_exists($targetClass) ) throw new Exception('Unknown class given');
        }
        else {
            $targetClass = getSession()->IsRDD() ? 'Issue' : 'Request';
        }

        $service = new TaskConvertToIssueService(getFactory(), $targetClass);
        $request_it = $service->convert($task_it);

        if ( $request_it->count() == 1 ) {
            echo $request_it->getViewUrl();
        }
        else {
        }
 	}
}