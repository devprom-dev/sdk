<?php

class FeatureHasIssuesRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
	    $featuresCount = getFactory()->getObject('FeatureType')->getRegistry()->Count(
	        array(
	            new FilterVpdPredicate(),
                new FilterAttributePredicate('HasIssues', 'Y')
            )
        );
	    if ( $featuresCount < 1 ) return parent::getFilters();

		return array_merge(
            parent::getFilters(),
            array (
                new FeatureIssuesAllowedFilter()
            )
		);
	}
}