<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessActionBuilder.php";
include "actions/RequestBusinessActionResolveTasks.php";
include "actions/RequestBusinessActionResetTasks.php";
include "actions/RequestBusinessActionAssignParticipant.php";
include "actions/RequestBusinessActionSetPriorityHigh.php";
include "actions/RequestBusinessActionResolveDuplicates.php";
include "actions/RequestBusinessActionGetInWorkDuplicates.php";
include "actions/RequestBusinessActionResolveImplemented.php";
include "actions/RequestBusinessActionGetInWorkImplementation.php";
include "actions/RequestBusinessActionMakeRealization.php";
include "actions/RequestBusinessActionMoveToProject.php";
include "actions/RequestBusinessActionMoveImplementedNextState.php";
include "actions/RequestBusinessActionMoveImplemented.php";
include "actions/BusinessActionIssueAutoActionShift.php";
include "actions/BusinessActionIssueAutoActionWorkflow.php";
include "actions/RequestBusinessActionSourceAutoActionWorkflow.php";
include "actions/RequestBusinessActionDependencyAutoActionWorkflow.php";
include "actions/RequestBusinessActionSuspectFeatureDocs.php";

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
 		$set->registerRule( new RequestBusinessActionSetPriorityHigh() );
 		$set->registerRule( new RequestBusinessActionResolveImplemented() );
 		$set->registerRule( new RequestBusinessActionGetInWorkImplementation() );
 		$set->registerRule( new RequestBusinessActionResolveDuplicates() );
 		$set->registerRule( new RequestBusinessActionGetInWorkDuplicates() );
		$set->registerRule( new RequestBusinessActionMoveImplementedNextState() );
        $set->registerRule( new RequestBusinessActionSuspectFeatureDocs() );
        $set->registerRule( new RequestBusinessActionMoveImplemented() );
        $set->registerRule( new RequestBusinessActionMakeRealization() );
        $set->registerRule( new RequestBusinessActionMoveToProject() );

        $it = getFactory()->getObject('IssueAutoAction')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('EventType', AutoActionEventRegistry::None),
                new FilterBaseVpdPredicate()
            )
        );
        while( !$it->end() ) {
            $set->registerRule( new BusinessActionIssueAutoActionWorkflow($it->copy()) );
            $set->registerRule( new RequestBusinessActionSourceAutoActionWorkflow($it->copy()) );
            $set->registerRule( new RequestBusinessActionDependencyAutoActionWorkflow($it->copy()) );
            $it->moveNext();
        }
    }
}