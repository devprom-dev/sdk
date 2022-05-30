<?php

class ProjectExceptCurrentRegistry extends ObjectRegistrySQL
{
	public function getFilters()
	{
        $vpds = getSession()->getAccessibleVpds();
        if ( count($vpds) < 1 ) $vpds = array(0);

        $filters = array_filter(parent::getFilters(), function($item) {
            return !$item instanceof FilterVpdPredicate && !$item instanceof FilterBaseVpdPredicate;
        });
		return array_merge( $filters,
            array (
                new ProjectVpdPredicate($vpds),
                new ProjectStatePredicate('active'),
                new FilterNotInPredicate(getSession()->getProjectIt()->getId())
            )
		);
	}
}