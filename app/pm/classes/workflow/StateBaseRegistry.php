<?php

class StateBaseRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array_merge(
				parent::getFilters(),
				array (
						new StateClassPredicate(strtolower($this->getObject()->getObjectClass()))
				)
		);
	}
}