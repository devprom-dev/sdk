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

class StateBusinessActionBuilderRequest extends StateBusinessActionBuilder
{
    public function getEntityRefName()
    {
        return 'pm_ChangeRequest';
    }
    
    public function build( StateBusinessActionRegistry & $set )
    {
        $request = getFactory()->getObject('Request');
    	
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
    }
}