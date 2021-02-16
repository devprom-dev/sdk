<?php
include_once SERVER_ROOT_PATH . "pm/classes/project/ProjectAccessibleRegistry.php";

class ProjectAccessibleActiveRegistry extends ProjectAccessibleRegistry
{
	public function getFilters()
	{
		return array_merge(
		    parent::getFilters(),
            array (
                new ProjectStatePredicate('active')
            )
		);
	}
}