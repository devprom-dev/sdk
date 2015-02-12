<?php

include "FunctionList.php";
include "FunctionTraceList.php";
include "FunctionChartList.php";

class FunctionTable extends PMPageTable
{
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

	function getActions()
	{
		$actions = array();
		
		$method = new ExcelExportWebMethod();
		$actions[] = array( 
				'name' => $method->getCaption(),
				'url' => $method->getJSCall( $this->getCaption(), 'IteratorExportExcel')
		);
		
		$method = new HtmlExportWebMethod();
		$actions[] = array( 
				'name' => $method->getCaption(),
				'url' => $method->getJSCall( 'IteratorExportHtml' )
		);
		
		return $actions;
	}

	function getNewActions()
	{
		$type_it = getFactory()->getObject('FeatureType')->getAll();
		
		if ( $type_it->count() < 1 ) return parent::getNewActions(); 

		$actions = array();
		
		$method = new ObjectCreateNewWebMethod($this->getObject());
		$method->setRedirectUrl('donothing');
		
		if ( !$method->hasAccess() ) return $actions;
		
		while( !$type_it->end() )
		{
			$uid = 'append-feature-'.$type_it->get('ReferenceName');
			$parms['Type'] = $type_it->getId();
			
			$actions[$uid] = array ( 
				'name' => $type_it->getDisplayName(),
				'uid' => $uid,
				'url' => $method->getJSCall($parms, $type_it->getDisplayName())
			);
			
			$type_it->moveNext();
		}
		
		return $actions;  
	}
	
	function getFilters()
	{
		$filters = array(
			new FunctionFilterStateWebMethod(),
			$this->buildFilterType(),
			new FilterTagWebMethod( getFactory()->getObject('FeatureTag') ),
			new FilterObjectMethod( getFactory()->getObject('Importance'), '', 'importance'),
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
			new FilterAttributePredicate( 'Importance', $filters['importance'] ),
			new FilterAttributePredicate( 'Type', $filters['type'] )
		);
		
		return array_merge(parent::getFilterPredicates(), $predicates);
	}
		
	function getViewFilter()
	{
		return new FunctionFilterViewWebMethod();
	}

	protected function buildFilterType()
	{
		$type_method = new FilterObjectMethod( getFactory()->getObject('FeatureType'), '', 'type');
		$type_method->setIdFieldName( 'ReferenceName' );
		return $type_method;
	}
}