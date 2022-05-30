<?php
include_once SERVER_ROOT_PATH."co/commands/c_bulkcomplete.php";
include_once SERVER_ROOT_PATH."pm/methods/SetTagsRequestWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/SetTagsWikiWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/SetTagsTaskWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/SetWatchersWebMethod.php";
include_once SERVER_ROOT_PATH . "pm/methods/TransitionStateMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/DuplicateIssuesWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/CloneWikiPageWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/UndoWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/TaskConvertToIssueWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/MarkChangesAsReadWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/ReintegrateWikiTraceWebMethod.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageModelExtendedBuilder.php";
include_once SERVER_ROOT_PATH."pm/methods/BindIssuesWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/MergeIssuesWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/methods/WikiExportOptionsWebMethod.php';

class BulkCompleteProject extends BulkComplete
{
 	function buildObject()
 	{
        getSession()->addBuilder( new \WikiPageModelExtendedBuilder() );
 		return parent::buildObject();
 	}
}
