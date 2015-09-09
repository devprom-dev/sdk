<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessRuleBuilder.php";

include "rules/IssueStateNonBlockedRule.php";
include "rules/IssueIsOwnerRule.php";
include "rules/IssueExactTypeRule.php";
include "rules/IssueBlockedWithinImplementation.php";

class StateBusinessRuleBuilderIssue extends StateBusinessRuleBuilder
{
    public function getEntityRefName()
    {
        return 'pm_ChangeRequest';
    }
    
    public function build( StateBusinessRuleRegistry & $set )
    {
     	$set->registerRule( new IssueStateNonBlockedRule() );
     	$set->registerRule( new IssueBlockedWithinImplementation() );
     	$set->registerRule( new IssueIsOwnerRule() );
 		
 		$type_it = getFactory()->getObject('pm_IssueType')->getRegistry()->Query(
 				array (
 						new FilterBaseVpdPredicate()
 				)
 		);
 		
 		while( !$type_it->end() )
 		{
 			$set->registerRule( new IssueExactTypeRule($type_it) );
 			$type_it->moveNext();
 		}
    }
}