<?php

class StateBaseRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array_merge(
				parent::getFilters(),
				array (
						new FilterAttributePredicate('ObjectClass', strtolower($this->getObject()->getObjectClass()))
				)
		);
	}
}