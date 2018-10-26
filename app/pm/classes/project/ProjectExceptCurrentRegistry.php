<?php

class ProjectExceptCurrentRegistry extends ObjectRegistrySQL
{
	public function getFilters()
	{
        $vpds = getSession()->getAccessibleVpds();
        if ( count($vpds) < 1 ) $vpds = array(0);

		return array_merge(
				parent::getFilters(),
				array (
                    new FilterVpdPredicate($vpds),
                    new ProjectStatePredicate('active'),
                    new FilterNotInPredicate(getSession()->getProjectIt()->getId())
				)
		);
	}
}