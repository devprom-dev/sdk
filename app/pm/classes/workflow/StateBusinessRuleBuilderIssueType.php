<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessRuleBuilder.php";
include "rules/IssueExactTypeRule.php";
include "rules/IssueReleaseRule.php";

class StateBusinessRuleBuilderIssueType extends StateBusinessRuleBuilder
{
    public function getEntityRefName() {
        return getSession()->IsRDD() ? 'Request' : 'pm_ChangeRequest';
    }
    
    public function build( StateBusinessRuleRegistry & $set )
    {
 		$type_it = getFactory()->getObject('RequestType')->getRegistry()->Query(
			array (
				new FilterBaseVpdPredicate()
			)
 		);
 		while( !$type_it->end() ) {
 			$set->registerRule( new IssueExactTypeRule($type_it) );
 			$type_it->moveNext();
 		}

        $releaseIt = getFactory()->getObject('ReleaseActual')->getAll();
        while( !$releaseIt->end() ) {
            $set->registerRule( new IssueReleaseRule($releaseIt) );
            $releaseIt->moveNext();
        }
    }
}