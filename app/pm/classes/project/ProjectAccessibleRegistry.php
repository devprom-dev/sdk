<?php

class ProjectAccessibleRegistry extends ObjectRegistrySQL
{
	public function getFilters()
	{
		return array_merge(
				parent::getFilters(),
				array (
						new ProjectAccessiblePredicate()
				)
		);
	}
}