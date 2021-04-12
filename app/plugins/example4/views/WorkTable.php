<?php

include "WorkTableList.php";

class WorkTable extends PageTable
{
	function getList()
	{
		return new WorkTableList($this->getObject());
	}

	function getTemplate()
	{
		return '../../plugins/example4/views/templates/WorkTable.tpl.php';
	}
	
	// returns the list of actions displayed top right the page
	function getActions()
	{
		return array();	
	}

	function getNewActions()
	{
		return array();	
	}

	function getDeleteActions()
	{
		return array();	
	}
	
 	function getSortDefault( $sort_parm = 'sort' )
	{
		switch( $sort_parm )
		{
		    case 'sort':
		    	return 'Priority';
		    	
		    case 'sort2':
		    	return '_group';
		    	
		    default:
		    	return parent::getSortDefault($sort_parm);
		}
	}
	
	// returns the list of actions used to setup filter
	function getFilterActions()
	{
		return array(array());
	}
	
	// returns the list of filters used to filter data of the table's list
	function getFilters()
	{
		return array(
				$this->buildProjectFilter(),
				$this->buildStateFilter(),
				$this->buildDepartmentFilter(),
				$this->buildCustomerFilter()
		);
	}
	
	function getFiltersDefault()
	{ 
		return array('any');
	}
	
	// returns the list of predicates used to filter list data
	function getFilterPredicates()
	{
		$values = $this->getFilterValues();
		
		if ( in_array($values['project'], array('', 'all', 'none')) )
		{
			$object = new WorkTableProject();
			
			$values['project'] = $object->getAll()->idsToArray();
		}
		
		return array (
				new FilterAttributePredicate('Project', $values['project']),
				new FilterAttributePredicate('State', preg_split('/-/', $values['state'])),
				new WorkTableDepartmentPredicate($values['department']),
				new WorkTableCustomerPredicate($values['customer'])
		);
	}
	
	private function buildProjectFilter()
	{
		$filter = new FilterObjectMethod( new WorkTableProject(), '', 'project' );

		$filter->setHasNone( false );
		
		return $filter;
	}
	
	private function buildDepartmentFilter()
	{
		$filter = new FilterObjectMethod( new WorkTableDepartment(), translate('Подразделение'), 'department' );

		return $filter;
	}
	
	private function buildCustomerFilter()
	{
		$filter = new FilterAutoCompleteWebMethod( new WorkTableCustomer(), 'Заказчик' );
		
		$filter->setValueParm( 'customer' );

		$filter->setModule($_SERVER['REDIRECT_URL']);
		
		return $filter;
	}
	
	private function buildStateFilter()
	{
		$object = new WorkTableMetaState();
		
		$state_it = $object->getAll();

		$nonterminal = array();
		
		while ( !$state_it->end() )
		{
			if ( $state_it->get('IsTerminal') == 'N' ) $nonterminal = $state_it->get('ReferenceName');
			
			$state_it->moveNext();
		}
		
		$state_it->moveFirst();
		
		$filter = new FilterObjectMethod( $state_it, translate('Состояние'), 'state' );

		$filter->setHasNone( false );
		
		$filter->setIdFieldName( 'ReferenceName' );
		
		$filter->setType( 'singlevalue' );
		
		$filter->setDefaultValue($nonterminal);
		
		return $filter;
	}

	function getCaption()
	{
		return 'Список запросов';
	}
	
	function drawFooter()
	{
	}
}