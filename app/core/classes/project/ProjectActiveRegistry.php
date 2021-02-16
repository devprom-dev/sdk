<?php

class ProjectActiveRegistry extends ObjectRegistrySQL
{
	public function getFilters()
	{
        $filters = array_filter(parent::getFilters(), function($item) {
            return !$item instanceof FilterVpdPredicate && !$item instanceof FilterBaseVpdPredicate;
        });
		return array_merge( $filters,
            array (
                new ProjectStatePredicate('active')
            )
		);
	}
}