<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessActionBuilder.php";
include "actions/WikiPageBusinessActionChildrenSyncState.php";
include "actions/WikiPageBusinessActionDocumentSyncState.php";
include "actions/WikiPageBusinessActionParentState.php";

class WikiPageBusinessActionBuilder extends StateBusinessActionBuilder
{
    public function getEntityRefName() {
        return 'WikiPage';
    }
    
    public function build( StateBusinessActionRegistry & $set )
    {
		$set->registerRule( new WikiPageBusinessActionChildrenSyncState() );
        $set->registerRule( new WikiPageBusinessActionDocumentSyncState() );
        $set->registerRule( new WikiPageBusinessActionParentState() );
    }
}