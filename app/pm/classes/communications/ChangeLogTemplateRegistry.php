<?php

class ChangeLogTemplateRegistry extends ChangeLogRegistry
{
	public function getFilters()
	{
		return array_merge(
				parent::getFilters(),
				array (
						new ChangeLogObjectFilter('request,task,pmblogpost,milestone,build,environment')
				)
		);
	}
}