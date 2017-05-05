<?php

class ProjectActiveRegistry extends ObjectRegistrySQL
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