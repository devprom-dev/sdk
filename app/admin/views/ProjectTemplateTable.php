<?php

include ('ProjectTemplateList.php');

class ProjectTemplateTable extends PageTable
{
	function getList()
	{
		return new ProjectTemplateList( $this->getObject() );
	}

 	function getSortFields()
	{
		return array();
	}
	
	function getFilters()
	{
		return array (
				$this->buildLanguageFilter()
		);
	}
	
	function buildLanguageFilter()
	{
		$filter = new FilterObjectMethod(getFactory()->getObject('cms_Language'), '', 'language');
		$filter->setDefaultValue(getSession()->getLanguage()->getLanguageId());
		return $filter;
	}
	
	function getFilterPredicates( $values )
	{
		return array (
				new FilterAttributePredicate('Language', $values['language'])
		);
	}

	function getCaption()
    {
        return translate('Процессы');
    }
}
