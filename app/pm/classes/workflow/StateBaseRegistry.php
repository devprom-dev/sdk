<?php
include "persisters/IssueStateDetailsPersister.php";

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

	function getPersisters()
    {
        $items = parent::getPersisters();
        if ( $this->getObject() instanceof IssueState ) {
            $items[] = new IssueStateDetailsPersister();
        }
        return $items;
    }
}