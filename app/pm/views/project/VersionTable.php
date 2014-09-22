<?php

include "VersionList.php";

include SERVER_ROOT_PATH."pm/classes/plan/CycleState.php";

class VersionTable extends PMPageTable
{
	function getList()
	{
		return new VersionList( $this->getObject() );
	}

	function getNewActions()
	{
		$actions = array();
		
		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Release'));
		
		$method->setRedirectUrl('donothing');
		
		if ( $method->hasAccess() )
		{
			$actions[] = array( 
					'name' => translate('Добавить релиз'),
					'url' => $method->getJSCall() 
			);
		}
		
		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Iteration'));
		
		$method->setRedirectUrl('donothing');
		
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() && $method->hasAccess() )
		{
			$actions[] = array();
			
			$actions[] = array(
					'name' => translate('Добавить итерацию'),
					'url' => $method->getJSCall() 
			);
		}
		
    	return $actions; 
	}
	
	function getSortFields()
	{
	    return array_intersect( parent::getSortFields(), array(
	            'VersionNumber', 
	            'Project', 
	            'EstimatedStartDate', 
	            'EstimatedFinishDate', 
	            'ActualStartDate', 
	            'ActualFinishDate'
	    ));
	}
	
	function getSortDefault( $sort_parm = 'sort' )
	{
		if ( $sort_parm == 'sort' )
		{
			return 'VersionNumber';
		}
		
		return 'none';
	}
	
	function getFilters()
	{
		return array_merge(parent::getFilters(), array (
			$this->getCycleStateFilter()
		));
	}
	
	function getFilterPredicates()
	{
	    $values = $this->getFilterValues();
	    
	    return array_merge( parent::getFilterPredicates(), array (
	            new StageTimelinePredicate($values['state'])
	    ));
	}
	
	function getCycleStateFilter()
	{
	    $filter = new FilterObjectMethod( new CycleState(), '', 'state' );
	    
	    $filter->setHasNone(false);
	    $filter->setType( 'singlevalue' );
	    $filter->setIdFieldName( 'ReferenceName' );
	    
	    $filter->setDefaultValue( 'not-passed' );
	    
	    return $filter;
	}
	
	function IsNeedToDelete() { return false; }
} 