<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class IntegrationTaskRunWebMethod extends WebMethod
{
 	function __construct( $object_it = null ) {
 		$this->object_it = $object_it;
 		parent::__construct();
 	}
 	
 	function getCaption() {
 		return translate('integration7');
 	}
 	
 	function getRedirectUrl()
 	{
		$job_it = getFactory()->getObject('co_ScheduledJob')->getByRef('ClassName', 'integration/integrationtask');
 		return '/tasks/command.php?class=runjobs&job='.$job_it->getId().'&chunk='.$this->object_it->getId().'&redirect='.urlencode($_SERVER['REQUEST_URI']);
 	}

 	function execute_request() {
        echo '/tasks/';
    }

    private $object_it = null;
}

