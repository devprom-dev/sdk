<?php
include_once SERVER_ROOT_PATH."co/commands/c_bulkcomplete.php";
include_once SERVER_ROOT_PATH."pm/methods/c_request_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/SetTagsRequestWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/SetTagsWikiWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/c_state_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/DuplicateIssuesWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/CloneWikiPageWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/c_wiki_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/UndoWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/TaskConvertToIssueWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/MarkChangesAsReadWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/classes/workflow/WorkflowModelBuilder.php';
include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageModelExtendedBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/model/validators/ModelNotificationValidator.php";
include_once SERVER_ROOT_PATH."pm/methods/BindIssuesWebMethod.php";

class BulkCompleteProject extends BulkComplete
{
 	function buildObject()
 	{
 		getSession()->addBuilder( new WorkflowModelBuilder() );
        getSession()->addBuilder( new \WikiPageModelExtendedBuilder() );
 		return parent::buildObject();
 	}

    function validate()
    {
        if ( !parent::validate() ) return false;

        $validator = new ModelNotificationValidator();
        $validator->validate($this->getObjectIt()->object, $_REQUEST);

        return true;
    }
}
