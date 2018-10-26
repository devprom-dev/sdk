<?php
include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBusinessRuleBuilder.php";
include "TaskWIPOverheadRule.php";

class KanbanTaskStateBusinessRuleBuilder extends StateBusinessRuleBuilder
{
    public function getEntityRefName() {
        return 'pm_Task';
    }
    
    public function build( StateBusinessRuleRegistry & $set ) {
		$set->registerRule( new TaskWIPOverheadRule() );
    }
}
