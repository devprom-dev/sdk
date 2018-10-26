<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessRuleBuilder.php";
include "rules/IssueStateNonBlockedRule.php";
include "rules/IssueIsOwnerRule.php";
include "rules/IssueExactTypeRule.php";
include "rules/IssueBlockedWithinImplementation.php";
include "rules/IssueBlockedByOpenTasks.php";
include "rules/IssueIsAuthorRule.php";
include "rules/IssueReleaseRule.php";
include "rules/IssuePriorityRule.php";

class StateBusinessRuleBuilderIssue extends StateBusinessRuleBuilder
{
    public function getEntityRefName() {
        return 'pm_ChangeRequest';
    }
    
    public function build( StateBusinessRuleRegistry & $set )
    {
     	$set->registerRule( new IssueStateNonBlockedRule() );
     	$set->registerRule( new IssueBlockedWithinImplementation() );
     	$set->registerRule( new IssueIsOwnerRule() );
		$set->registerRule( new IssueBlockedByOpenTasks() );
		$set->registerRule( new IssueIsAuthorRule() );
 		
 		$type_it = getFactory()->getObject('pm_IssueType')->getRegistry()->Query(
			array (
				new FilterBaseVpdPredicate()
			)
 		);
		$set->registerRule( new IssueExactTypeRule() );
 		while( !$type_it->end() ) {
 			$set->registerRule( new IssueExactTypeRule($type_it) );
 			$type_it->moveNext();
 		}

 		$releaseIt = getFactory()->getObject('ReleaseActual')->getAll();
 		while( !$releaseIt->end() ) {
            $set->registerRule( new IssueReleaseRule($releaseIt) );
            $releaseIt->moveNext();
        }

        $priorityIt = getFactory()->getObject('Priority')->getAll();
        while( !$priorityIt->end() ) {
            $set->registerRule( new IssuePriorityRule($priorityIt) );
            $priorityIt->moveNext();
        }
    }
}