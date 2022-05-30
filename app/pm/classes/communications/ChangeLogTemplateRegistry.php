<?php

class ChangeLogTemplateRegistry extends ChangeLogRegistry
{
	public function getFilters()
	{
		return array_merge(
				parent::getFilters(),
				array (
                    new ChangeLogObjectFilter('request,task,milestone,build,environment'),
                    new ChangeLogStartFilter(
                        getSession()->getLanguage()->getPhpDate(
                            strtotime('-1 week', strtotime(date('Y-m-j'))))
                    )
				)
		);
	}
}