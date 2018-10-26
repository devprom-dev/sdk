<?php

class ProjectTemplateRegistryProcloud extends ObjectRegistrySQL
{
	public function getFilters()
	{
		return array_merge( parent::getFilters(), array ( 
				new FilterAttributePredicate('ProductEdition', 'team') 
		));
	}
}