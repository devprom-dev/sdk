<?php

class ProjectLinkedActiveRegistry extends ProjectLinkedRegistry
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