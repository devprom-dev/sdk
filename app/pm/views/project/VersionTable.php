<?php
include "VersionList.php";
include "VersionTree.php";

class VersionTable extends PMPageTable
{
	function getList() {
		return new VersionTree( $this->getObject() );
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
			return 'ActualStartDate';
		}
		
		return 'none';
	}
	
	function getFilters()
	{
		return array_merge(
		    parent::getFilters(),
            array (
                $this->buildStartFilter(),
			    $this->buildFinishFilter(),
			    $this->getCycleStateFilter()
		    )
        );
	}
	
	function getFilterPredicates( $values )
	{
	    return array_merge(
	        parent::getFilterPredicates( $values ),
            array(
                new FilterDateAfterPredicate('EstimatedFinishDate', $values['start']),
                new FilterDateBeforePredicate('EstimatedFinishDate', $values['finish']),
                new StageTimelinePredicate($values['state']),
                $_REQUEST['roots'] == '0'
                    ? new FilterAttributeNullPredicate('ParentStage')
                    : new FilterAttributePredicate('ParentStage', $_REQUEST['roots']),
                $_REQUEST['roots'] == '0'
                    ? new FilterAttributeNullPredicate('ParentStageClass')
                    : new FilterAttributePredicate('ParentStageClass', $_REQUEST['rootclass'])
            )
        );
	}
	
	function getCycleStateFilter()
	{
	    $filter = new FilterObjectMethod( new CycleState(), '', 'state' );
	    $filter->setHasNone(false);
        $filter->setDefaultValue('not-passed');
	    $filter->setType( 'singlevalue' );
	    $filter->setIdFieldName( 'ReferenceName' );
	    return $filter;
	}

    function buildStartFilter() {
        return new FilterDateWebMethod(translate('Начало'), 'start');
    }

    function buildFinishFilter()
    {
        if( array_key_exists('finish',$_REQUEST) and in_array($_REQUEST['finish'],array('','hide')) ) {
            unset($_REQUEST['finish']);
        }
        $filter = new ViewFinishDateWebMethod();
        return $filter;
    }

    public function buildFilterValuesByDefault( & $filters )
    {
        $values = parent::buildFilterValuesByDefault( $filters );
        if ( $values['start'] == '' ) {
            $values['start'] = getSession()->getLanguage()->getPhpDate(strtotime('-1 weeks', strtotime(date('Y-m-j'))));
        }
        return $values;
    }

    function getExportActions() {
        return array();
    }

    protected function getFamilyModules( $module )
    {
        switch( $module ) {
            case 'project-plan-hierarchy':
                return array (
                    'delivery',
                    'releases',
                    'iterations',
                    'milestones',
                    'tasksplanningboard',
                    'iterationplanningboard',
                    'releaseplanningboard',
                    'assignedtasks',
                    'projects',
                    'process/metrics'
                );
            default:
                return parent::getFamilyModules($module);
        }
    }

    protected function getChartModules( $module )
    {
        return array (
            'resman/resourceload',
            'projectburnup',
            'iterationburndown',
            'scrum/velocitychart',
            'workitemchart'
        );
    }

    function getReferencesListWidget( $parm, $referenceName )
    {
        $object = $parm instanceof OrderedIterator ? $parm->object : $parm;
        if ( $object instanceof Task ) {
            if ( is_object($this->tasks_widget) ) return $this->tasks_widget;
            $report = getFactory()->getObject('PMReport');
            return $this->tasks_widget = $report->getExact('tasks-trace');
        }
        if ( $object instanceof Request ) {
            if ( is_object($this->issues_widget) ) return $this->issues_widget;
            $report = getFactory()->getObject('PMReport');
            return $this->issues_widget = $report->getExact('issues-trace');
        }
        return parent::getReferencesListWidget( $parm, $referenceName );
    }

    function getImportActions()
    {
        return array();
    }
}