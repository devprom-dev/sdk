<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessRuleBuilder.php";
include "rules/IssueExactTypeRuleObsolete.php";

class StateBusinessRuleBuilderObsolete extends StateBusinessRuleBuilder
{
    public function getEntityRefName() {
        return 'Comment';
    }
    
    public function build( StateBusinessRuleRegistry & $set )
    {
 		$type_it = getFactory()->getObject('pm_IssueType')->getRegistry()->Query(
			array (
				new FilterBaseVpdPredicate()
			)
 		);
		$set->registerRule( new IssueExactTypeRuleObsolete() );
 		while( !$type_it->end() ) {
 			$set->registerRule( new IssueExactTypeRuleObsolete($type_it) );
 			$type_it->moveNext();
 		}
    }
}