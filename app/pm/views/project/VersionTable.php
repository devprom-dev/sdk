<?php
include SERVER_ROOT_PATH."pm/classes/plan/CycleState.php";
include "VersionList.php";

class VersionTable extends PMPageTable
{
	function getList()
	{
		return new VersionList( $this->getObject() );
	}

	function getNewActions()
	{
		$actions = array();
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Iteration'));
		if ( $methodology_it->HasPlanning() && $method->hasAccess() ) {
			$actions[] = array(
					'name' => translate('Итерация'),
					'url' => $method->getJSCall() 
			);
		}
		
		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Release'));
		if ( $methodology_it->HasReleases() && $method->hasAccess() ) {
			$actions[] = array(
					'name' => translate('Релиз'),
					'url' => $method->getJSCall() 
			);
		}

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Milestone'));
        if ( $method->hasAccess() ) {
            $actions[] = array(
                'name' => translate('Веха'),
                'url' => $method->getJSCall()
            );
        }

    	return $actions; 
	}
	
	function getBulkActions()
	{
		return array_merge(
				parent::getBulkActions(),
				array (
						'modify' => array()
				)
		);
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

	function getExportActions() {
        return array();
    }

    protected function getFamilyModules( $module )
    {
        switch( $module ) {
            case 'project-plan-hierarchy':
                return array (
                    'ee/delivery'
                );
            default:
                return parent::getFamilyModules($module);
        }
    }
}