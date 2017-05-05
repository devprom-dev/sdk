<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessActionBuilder.php";

include "actions/RequestBusinessActionResolveTasks.php";
include "actions/RequestBusinessActionResetTasks.php";
include "actions/RequestBusinessActionAssignParticipant.php";
include "actions/RequestBusinessActionResetAssignee.php";
include "actions/RequestBusinessActionSetPriorityHigh.php";
include "actions/RequestBusinessActionResolveDuplicates.php";
include "actions/RequestBusinessActionGetInWorkDuplicates.php";
include "actions/RequestBusinessActionResolveImplemented.php";
include "actions/RequestBusinessActionGetInWorkImplementation.php";
include "actions/RequestBusinessActionMoveImplementedNextState.php";
include "actions/BusinessActionIssueAutoActionShift.php";
include "actions/BusinessActionIssueAutoActionWorkflow.php";

class StateBusinessActionBuilderRequest extends StateBusinessActionBuilder
{
    public function getEntityRefName()
    {
        return 'pm_ChangeRequest';
    }
    
    public function build( StateBusinessActionRegistry & $set )
    {
		$set->registerRule( new RequestBusinessActionResolveTasks() );
		$set->registerRule( new RequestBusinessActionResetTasks() );
   		$set->registerRule( new RequestBusinessActionAssignParticipant() );
		$set->registerRule( new RequestBusinessActionResetAssignee() );
 		$set->registerRule( new RequestBusinessActionSetPriorityHigh() );
 		$set->registerRule( new RequestBusinessActionResolveImplemented() );
 		$set->registerRule( new RequestBusinessActionGetInWorkImplementation() );
 		$set->registerRule( new RequestBusinessActionResolveDuplicates() );
 		$set->registerRule( new RequestBusinessActionGetInWorkDuplicates() );
		$set->registerRule( new RequestBusinessActionMoveImplementedNextState() );

        $eventTypes = array(
            AutoActionEventRegistry::CreateAndModify,
            AutoActionEventRegistry::ModifyOnly,
            AutoActionEventRegistry::CreateOnly
        );

        $it = getFactory()->getObject('IssueAutoAction')->getAll();
        while( !$it->end() ) {
            $set->registerRule(
                in_array($it->get('EventType'), $eventTypes)
                    ? new BusinessActionIssueAutoActionShift($it->copy())
                    : new BusinessActionIssueAutoActionWorkflow($it->copy())
            );
            $it->moveNext();
        }
    }
}