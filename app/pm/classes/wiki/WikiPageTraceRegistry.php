<?php

class WikiPageTraceRegistry extends ObjectRegistrySQL
{
	function getFilters()
	{
		$filters = parent::getFilters();
		
		$ref_name = $this->getObject()->getTargetReferenceName();
		
		if ( $ref_name != '' )
		{
			$filters[] = new WikiTraceTargetReferencePredicate($ref_name);
		}
		
		return $filters;
	}
}