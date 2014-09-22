<?php

include "LeadAndCycleTimeChart.php";

include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedBeforeDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedAfterDateWebMethod.php";

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
 			new FilterAttributePredicate( 'Type', $values['type'] ),
 			new FilterAttributePredicate( 'Priority', $values['priority'])
 		);
 	}

	function getFilters()
	{
		global $model_factory;
		
		$filters = array(
			new ViewSubmmitedAfterDateWebMethod(),
			new ViewSubmmitedBeforeDateWebMethod(),
			new FilterObjectMethod( $model_factory->getObject('Priority'), '', 'priority'),
			$this->buildTypeFilter()
		);
		
		return array_merge( $filters, parent::getFilters() ); 		
	}
	
	function buildTypeFilter()
	{
		global $model_factory;
		
		$type_method = new FilterObjectMethod( $model_factory->getObject('pm_IssueType'), translate('Òèï'), 'type');
		
		$type_method->setIdFieldName( 'ReferenceName' );
		
		$type_method->setNoneTitle( $model_factory->getObject('Request')->getDisplayName() );
		
		return $type_method;
	}
} 