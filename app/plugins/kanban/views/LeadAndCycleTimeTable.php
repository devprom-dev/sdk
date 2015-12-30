<?php

include "LeadAndCycleTimeChart.php";

include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedBeforeDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedAfterDateWebMethod.php";
include_once SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';

class LeadAndCycleTimeTable extends PMPageTable
{
	function getList()
	{
		return new LeadAndCycleTimeChart( $this->getObject() );
	}

 	function getFilterPredicates()
 	{
 		$values = $this->getFilterValues();
 		
 		return array (
			new FilterSubmittedAfterPredicate($values['submittedon']),
			new FilterSubmittedBeforePredicate($values['submittedbefore']),
			new RequestFinishAfterPredicate($values['modifiedafter']),
			new RequestAuthorFilter( $values['author'] ),
 			new FilterAttributePredicate('Type', $values['type']),
 			new FilterAttributePredicate('Priority', $values['priority'])
 		);
 	}

	function getFilters()
	{
		$filters = array(
			new ViewSubmmitedAfterDateWebMethod(),
			new ViewSubmmitedBeforeDateWebMethod(),
			new ViewModifiedAfterDateWebMethod(),
			new FilterObjectMethod( getFactory()->getObject('Priority'), '', 'priority'),
			$this->buildTypeFilter(),
			$this->buildFilterAuthor()
		);
		return array_merge( $filters, parent::getFilters() );
	}
	
	function buildTypeFilter()
	{
		$type_method = new FilterObjectMethod( getFactory()->getObject('pm_IssueType'), translate('Тип'), 'type');
		$type_method->setIdFieldName( 'ReferenceName' );
		$type_method->setNoneTitle( getFactory()->getObject('Request')->getDisplayName() );
		return $type_method;
	}

	protected function buildFilterAuthor()
	{
		$author = getFactory()->getObject('IssueActualAuthor');
		$filter = new FilterAutoCompleteWebMethod($author, translate('Автор'), 'author');
		$filter->setIdFieldName('Login');
		return $filter;
	}
}