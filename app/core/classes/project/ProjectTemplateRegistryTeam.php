<?php

class ProjectTemplateRegistryTeam extends ObjectRegistrySQL
{
	public function getFilters()
	{
		return array_merge( parent::getFilters(), array ( 
				new ProjectTemplateExceptEditionPredicate('ee') 
		));
	}
}