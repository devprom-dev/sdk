<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessActionBuilder.php";
include "actions/WikiPageBusinessActionReturnToWork.php";
include "actions/WikiPageBusinessActionShiftBreakTraces.php";
include "actions/WikiPageBusinessActionBreakTraces.php";

class StateBusinessActionBuilderWikiPage extends StateBusinessActionBuilder
{
    public function getEntityRefName()
    {
        return 'WikiPage';
    }
    
    public function build( StateBusinessActionRegistry & $set )
    {
 		$set->registerRule( new WikiPageBusinessActionReturnToWork() );
        $set->registerRule( new WikiPageBusinessActionShiftBreakTraces() );
        $set->registerRule( new WikiPageBusinessActionBreakTraces() );
    }
}