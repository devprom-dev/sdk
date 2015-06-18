<?php

class ProjectExceptCurrentRegistry extends ObjectRegistrySQL
{
	public function getFilters()
	{
		return array_merge(
				parent::getFilters(),
				array (
						new FilterNotInPredicate(getSession()->getProjectIt()->getId())
				)
		);
	}
}