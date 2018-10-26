<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessRuleBuilder.php";
include "IssueWIPOverheadRule.php";

class KanbanIssueStateBusinessRuleBuilder extends StateBusinessRuleBuilder
{
    public function getEntityRefName() {
        return 'pm_ChangeRequest';
    }
    
    public function build( StateBusinessRuleRegistry & $set ) {
		$set->registerRule( new IssueWIPOverheadRule() );
    }
}
