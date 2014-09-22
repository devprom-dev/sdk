<?php

class CustomReportMyRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		return array_merge( 
				array (
						new CustomReportMyPredicate(),
						new FilterBaseVpdPredicate(),
						new FilterAttributePredicate('Category', FUNC_AREA_FAVORITES)
				),
				parent::getFilters()
		);
	}
}