<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessRuleBuilder.php";

include "rules/TaskStateNonBlockedRule.php";
include "rules/TaskIsAssigneeRule.php";
include "rules/TaskExactTypeRule.php";

class StateBusinessRuleBuilderTask extends StateBusinessRuleBuilder
{
    public function getEntityRefName()
    {
        return 'pm_Task';
    }
    
    public function build( StateBusinessRuleRegistry & $set )
    {
     	$set->registerRule( new TaskStateNonBlockedRule() );
 		
     	$set->registerRule( new TaskIsAssigneeRule() );
 		
 		$type_it = getFactory()->getObject('pm_TaskType')->getRegistry()->Query(
 				array (
 						new FilterBaseVpdPredicate()
 				)
 		);
 		
 		while( !$type_it->end() )
 		{
 			$set->registerRule( new TaskExactTypeRule($type_it) );
 			
 			$type_it->moveNext();
 		}
    }
}
