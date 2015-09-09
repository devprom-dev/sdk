<?php
include_once SERVER_ROOT_PATH."pm/classes/issues/sorts/IssueProjectSortClause.php";

class RequestAsTargetRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		$project_it = getFactory()->getObject('Project')->getRegistry()->Query(
				array ( new ProjectAccessiblePredicate() )
		);
		return array ( 
				new FilterVpdPredicate($project_it->fieldToArray('VPD'))
		);
	}

	function getSorts()
	{
		return array_merge(
			parent::getSorts(),
			array (
				new IssueProjectSortClause()
			)
		);
	}
}