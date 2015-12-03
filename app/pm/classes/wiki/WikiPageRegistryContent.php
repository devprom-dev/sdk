<?php

class WikiPageRegistryContent extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array_merge(parent::getFilters(),
			array (
				new FilterAttributePredicate('ReferenceName', $this->getObject()->getReferenceName()),
				new FilterAttributePredicate('IsTemplate', 0),
			));
	}
}