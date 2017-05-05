<?php
include_once SERVER_ROOT_PATH . "pm/classes/project/predicates/ProjectAccessibleVpdPredicate.php";

class ProjectAccessibleRegistry extends ObjectRegistrySQL
{
	public function getFilters()
	{
		return array_merge(
				parent::getFilters(),
				array (
						new ProjectAccessibleVpdPredicate()
				)
		);
	}
}