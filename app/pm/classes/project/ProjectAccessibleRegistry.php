<?php
include_once SERVER_ROOT_PATH . "pm/classes/project/predicates/ProjectAccessibleVpdPredicate.php";

class ProjectAccessibleRegistry extends ObjectRegistrySQL
{
	public function getFilters()
	{
	    $filters = array_filter(parent::getFilters(), function($item) {
            return !$item instanceof FilterVpdPredicate && !$item instanceof FilterBaseVpdPredicate;
        });
		return array_merge( $filters,
            array (
                new ProjectAccessibleVpdPredicate()
            )
		);
	}
}