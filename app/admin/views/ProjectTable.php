<?php

include ('ProjectList.php');

class ProjectTable extends PageTable
{
    function getSection()
    {
        return 'admin';
    }

	function getList()
	{
		return new ProjectList( $this->object );
	}

    function getCaption()
	{
		return '';
	}

	function getFilterPredicates()
	{
		$values = $this->getFilterValues();
		
		return array_merge(
				parent::getFilterPredicates(),
				array (
					new ProjectStatePredicate($values['state'])
				)
		);
	}
	
	function getFilters()
	{
		return array(
			new FilterAutoCompleteWebMethod($this->getObject(), translate('Название проекта') ),
			$this->buildStateFilter()
		);
	}
	
	function buildStateFilter()
	{
		$filter = new FilterObjectMethod(getFactory()->getObject('ProjectState'), '', 'state');
		$filter->setIdFieldName('ReferenceName');
		$filter->setHasNone(false);
		$filter->setType('singlevalue');
		$filter->setDefaultValue('active');
		return $filter;
	}

	function getSortDefault( $sort_parm = 'sort' )
	{
	    if ( $sort_parm == 'sort' ) return 'Caption';
	    
	    return parent::getSortDefault( $sort_parm );
	}
	
	function getNewActions()
	{
		if ( !getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Project')) ) return array();
			
		return array(
				array (
						'name' => translate('Создать'),
						'url' => '/projects/new'
				)
		);
	}
	
	function getDeleteActions()
	{
	    return array();
	}
	
 	function getDefaultRowsOnPage()
	{
		return 60;
	}
}
