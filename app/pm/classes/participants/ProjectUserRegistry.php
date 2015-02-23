<?php

class ProjectUserRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array_merge(
				parent::getFilters(),
				array (
						new UserWorkerPredicate()
				)
		);
	}
}