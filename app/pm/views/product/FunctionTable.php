<?php

include "FunctionList.php";
include "FunctionTraceList.php";
include "FunctionChartList.php";

class FunctionTable extends PMPageTable
{
    var $object;
    
	function getList( $mode = '' )
	{
		switch ( $mode )
		{
			case 'trace':
				return new FunctionTraceList( $this->getObject() );

			case 'chart':
				return new FunctionChartList( $this->getObject() );
				
			default:
				return new FunctionList( $this->getObject() );
		}
	}

	function getSortDefault( $sort_parm )
	{
		if ( $sort_parm == 'sort' )
		{
			return 'Importance';
		}
		
		return parent::getSortDefault( $sort_parm );
	}
	
	function getActions()
	{
		global $model_factory;
		
		$actions = array();
		
		$method = new ExcelExportWebMethod();

		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->getJSCall( $this->getCaption(), 'IteratorExportExcel') ) );
		
		$method = new HtmlExportWebMethod();

		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->getJSCall( 'IteratorExportHtml' ) ) );
		
		return $actions;
	}
	
	function getFilters()
	{
		global $model_factory;
		
		$filters = array(
			new FunctionFilterStateWebMethod(),
			new FilterTagWebMethod( $model_factory->getObject('FeatureTag') ),
			new FilterObjectMethod( $model_factory->getObject('Importance'), '', 'importance'),
			new FunctionFilterStageWebMethod()
			);

		$view = new FunctionFilterViewWebMethod();
		
		$view->setFilter( $this->getFiltersName() );
		
		if ( $view->getValue() == 'chart' )
		{
			array_push($filters, new ViewDateYearWebMethod() );
		}
		
		return array_merge( $filters, parent::getFilters() );
	}
	
	function getFilterPredicates()
	{
	    $filters = $this->getFilterValues();
	    
		$predicates = array(
			new FeatureStateFilter( $filters['state'] ),
			new FeatureStageFilter( $filters['stage'] ),
			new CustomTagFilter( $this->getObject(), $filters['tag'] ),
			new FilterAttributePredicate( 'Importance', $filters['importance'] )
		);
		
		return array_merge(parent::getFilterPredicates(), $predicates);
	}
		
	function getViewFilter()
	{
		return new FunctionFilterViewWebMethod();
	}
}