<?php
 
include_once SERVER_ROOT_PATH."co/commands/c_bulkcomplete.php";
include_once SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/c_state_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/DuplicateIssuesWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/c_wiki_methods.php";
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowModelBuilder.php';

////////////////////////////////////////////////////////////////////////////////////////////////////
class BulkCompleteProject extends BulkComplete
{
 	function getObjectIt()
 	{
 		getSession()->addBuilder( new WorkflowModelBuilder() );
 		return parent::getObjectIt();
 	}
}
