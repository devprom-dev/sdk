<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessActionBuilder.php";
include "actions/TaskBusinessActionResolveIssue.php";
include "actions/TaskBusinessActionDeclineIssue.php";
include "actions/TaskBusinessActionAssignParticipant.php";
include "actions/TaskBusinessActionResetAssignee.php";
include "actions/TaskBusinessActionReopenIssue.php";
include "actions/TaskBusinessActionGetIssueInWork.php";
include "actions/TaskBusinessActionMoveIssueNextState.php";

class StateBusinessActionBuilderTask extends StateBusinessActionBuilder
{
    public function getEntityRefName()
    {
        return 'pm_Task';
    }
    
    public function build( StateBusinessActionRegistry & $set )
    {
 		$set->registerRule( new TaskBusinessActionResolveIssue() );
 		$set->registerRule( new TaskBusinessActionDeclineIssue() );
 		$set->registerRule( new TaskBusinessActionAssignParticipant() );
 		$set->registerRule( new TaskBusinessActionResetAssignee() );
 		$set->registerRule( new TaskBusinessActionReopenIssue() );
        $set->registerRule( new TaskBusinessActionGetIssueInWork() );
        $set->registerRule( new TaskBusinessActionMoveIssueNextState() );
    }
}