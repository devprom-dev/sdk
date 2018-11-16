<?php

include "TaskList.php";
include "TaskTraceList.php";
include "TaskBoardList.php";
include "TaskBoardPlanning.php";
include "IteratorExportTaskBoard.php";
include_once SERVER_ROOT_PATH.'pm/methods/c_task_methods.php';
include_once SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';
include_once SERVER_ROOT_PATH."pm/methods/FilterStateTransitionMethod.php";
include_once SERVER_ROOT_PATH."pm/views/plan/FilterReleaseMethod.php";
include_once SERVER_ROOT_PATH."pm/views/plan/FilterIterationMethod.php";

class TaskTable extends PMPageTable
{
	var $workload = array();
	private $estimation_strategy = null;
	private $uidService = null;

	function getViewFilter()
	{
		return new ViewTaskListWebMethod();
	}
	
	function buildRelatedDataCache()
	{
		$this->estimation_strategy = new EstimationHoursStrategy();
		$this->uidService = new ObjectUID();

		$this->cacheTraces('IssueTraces');
	}

    function getListIterator()
    {
        switch( $this->getReportBase() ) {
            case 'tasksplanbyfact':
            case 'iterationburndown':
                return ViewTable::getListIterator();
        }
        return parent::getListIterator();
    }


    function getUidService() {
		return $this->uidService;
	}

	function getPredicates($filters)
	{
		$predicates = parent::getPredicates($filters);

		if ( !$this->getTable()->hasCrossProjectFilter() ) {
			$predicates[] = new FilterBaseVpdPredicate();
		}
		
		return $predicates;
	}
	
	function getAssigneeUserWorkloadData()
	{
		return $this->workload;
	}
	
	function getList( $mode = '' )
	{
		switch ( $mode )
		{
			case '':
			case 'list':
			case 'tasks':
				return new TaskList( $this->getObject() );

			case 'trace':
				return new TaskTraceList( $this->getObject() );
				
			case 'chart':
				switch($_REQUEST['report'])
				{
					case 'tasksplanbyfact':
                    case 'iterationburndown':
                        return new TaskList( $this->getObject() );
					default:
						return new TaskChart( $this->getObject() );
				}

			default:
				switch($this->getPage()->getReportBase()) {
					case 'tasksplanningboard':
						return new TaskBoardPlanning( $this->getObject() );
					default:
						return new TaskBoardList( $this->getObject(), $this->is_finished );
				}
		}
	}
	
	function hasCrossProjectFilter()
	{
        if ( getSession()->getProjectIt()->IsPortfolio() ) return true;
        if ( $this->getReportBase() == 'iterationplanningboard' ) return true;
		return parent::hasCrossProjectFilter();
	}
	
  	function getSortAttributeClause( $field )
	{
	    $parts = preg_split('/\./', $field);
	    
	    if ( $parts[0] == 'Assignee' )
	    {
	        return new TaskAssigneeSortClause();
	    }
	    
		return parent::getSortAttributeClause( $field );
	}
	
 	function getFilters()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		$filters = array(
			$this->buildFilterState(),
			$this->buildTypeFilter(),
			$this->buildTagsFilter(),
			new FilterObjectMethod( getFactory()->getObject('Priority'), '', 'taskpriority' ),
        );

        if ( $methodology_it->HasPlanning() ) {
            $filters[] = $this->buildIterationFilter();
        }
        if ( $methodology_it->HasReleases() ) {
            $filters[] = $this->buildReleaseFilter();
        }

        $assigneeFilter = $this->buildAssigneeFilter();
        if ( is_object($assigneeFilter) ) {
            $filters[] = $assigneeFilter;
        }

        $filters = array_merge($filters, array(
			new ViewSubmmitedAfterDateWebMethod(),
			new ViewSubmmitedBeforeDateWebMethod(),
            new ViewModifiedAfterDateWebMethod(),
			new ViewModifiedBeforeDateWebMethod(),
            new FilterDateWebMethod(text(2539), 'finishedafter'),
			new FilterDateWebMethod(text(2538), 'finishedbefore'),
			new FilterAutoCompleteWebMethod(getFactory()->getObject('Request'), '', 'issue')
		));

		$filter = $this->buildUserGroupFilter();
		if ( is_object($filter) ) $filters[] = $filter;
		$filters[] = $this->buildUserRoleFilter();

		if ( $methodology_it->HasFeatures() ) {
			$filters[] = $this->buildFilterFunction();
		}
        $filters[] = $this->buildIssueStateFilter();
        $filters[] = $this->buildAuthorFilter();
        $filters[] = $this->buildDeadlineFilter();

		return array_merge( $filters, PMPageTable::getFilters() ); 		
	}

	protected function buildTypeFilter()
	{
	    $taskType = getFactory()->getObject('pm_TaskType');
		$type_method = new FilterObjectMethod( $taskType, translate('Тип'), 'tasktype');
		$type_method->setIdFieldName( 'ReferenceName' );
		return $type_method;
	}

	protected function buildDeadlineFilter()
    {
        return new FilterDateWebMethod(translate('Завершить к'), 'plannedfinish');
    }

	protected function buildIssueStateFilter()
    {
        $filter = new StateExFilterWebMethod(
            WorkflowScheme::Instance()->getStateIt(
                getFactory()->getObject('Request')
            ),
            'issueState',
            ''
        );
        $filter->setDefaultValue('');
        $filter->setCaption(text(2128));
        return $filter;
    }
	
	protected function buildAssigneeFilter()
	{
		$user_it = getFactory()->getObject('ProjectUser')->getAll();
		if ( $user_it->count() < 1 ) {
			$user_it = getFactory()->getObject('User')->getRegistry()->Query(
				array( new FilterInPredicate(getSession()->getUserIt()->getId()) )
			);
		}
		return new FilterObjectMethod( $user_it, text(753), 'taskassignee' );
	}

    protected function buildAuthorFilter()
    {
        $user_it = getFactory()->getObject('ProjectUser')->getAll();
        return new FilterObjectMethod( $user_it, translate('Автор'), 'author' );
    }

    protected function buildTagsFilter()
    {
        $tag = getFactory()->getObject('TaskTag');
        $filter = new FilterObjectMethod($tag, translate('Тэги'), 'tag');
        $filter->setIdFieldName('Tag');
        return $filter;
    }

	protected function buildIterationFilter()
	{
        $filter = new FilterIterationMethod();
		if ( $this->getReportBase() == 'iterationburndown' ) {
            $filter->setType('singlevalue');
            $filter->setHasNone(false);
            $filter->setHasAll(false);
            $filter->setDefaultValue(getFactory()->getObject('IterationActual')->getFirst()->getId());
        }
        return $filter;
	}

    protected function buildReleaseFilter()
    {
        $filter = new FilterReleaseMethod('issue-release');
        return $filter;
    }

	protected function buildFilterFunction()
	{
		$filter = new FilterObjectMethod(getFactory()->getObject('Feature'), '', 'function');
		return $filter;
	}

	function getFilterValues()
    {
        $values = parent::getFilterValues();
        if ( in_array($values['state'], array('','hide','all')) && !in_array($values['taskstate'], array('','hide','all')) ) {
            $values['state'] = $values['taskstate'];
        }
        return $values;
    }

 	function getFilterPredicates()
	{
		$values = $this->getFilterValues();

		$predicates = array(
			$this->buildStatePredicate($values['state']),
            $this->buildAssigneePredicate($values),
			new FilterAttributePredicate( 'Priority', $values['taskpriority'] ),
			new FilterAttributePredicate( 'TaskType', $values['tasktype'] ),
			new FilterAttributePredicate( 'Release', $values['iteration'] ),
            new FilterAttributePredicate( 'Author', $values['author'] ),
			new TaskReleasePredicate($values['issue-release']),
 			new TaskVersionPredicate( $values['stage'] ),
			new FilterSubmittedAfterPredicate( $values['submittedon'] ),
			new FilterSubmittedBeforePredicate( $values['submittedbefore'] ),
			new FilterDateAfterPredicate('FinishDate', $values['finishedafter']),
            new FilterDateBeforePredicate('FinishDate', $values['finishedbefore']),
			new FilterAttributePredicate( 'ChangeRequest', $_REQUEST['issue'] != '' ? $_REQUEST['issue'] : $values['issue'] ),
			new TaskFeaturePredicate($_REQUEST['function'] != '' ? $_REQUEST['function'] : $values['function']),
            new TaskIssueStatePredicate($_REQUEST['issueState'] != '' ? $_REQUEST['issueState'] : $values['issueState']),
            new CustomTagFilter( $this->getObject(), $values['tag'] ),
            $this->buildDeadlinePredicate($values)
		);		

		$predicates[] = new FilterModifiedAfterPredicate($values['modifiedafter']);
		$predicates[] = new FilterModifiedBeforePredicate($values['modifiedbefore']);
 		$predicates[] = new TaskBindedToObjectPredicate($_REQUEST['trace']);

		return array_merge(parent::getFilterPredicates(), $predicates);
	}

    function buildDeadlinePredicate( $values ) {
	    return new FilterDateBeforePredicate('PlannedFinishDate', $values['plannedfinish']);
    }

	function buildAssigneePredicate( $values ) {
        return new FilterAttributePredicate( 'Assignee', $this->getFilterUsers($values['taskassignee'],$values) );
    }

	function getActions()
	{
		$actions = array();

		$module = getFactory()->getObject('Module');

		$method = new BoardExportWebMethod();
		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->url( 'IteratorExportTaskBoard' ) ) );

		$module_it = $module->getExact('attachments');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$item = $module_it->buildMenuItem('class=task');
			if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
			$actions[] = array(
					'name' => text(1373),
					'url' => $item['url']
			);
		}

		$module_it = $module->getExact('tasks-import');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) && !getSession()->getProjectIt()->IsPortfolio() )
		{
			if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
			$item = $module_it->buildMenuItem('?view=import&mode=xml&object=task');
			$actions[] = array(
				'name' => translate('Импортировать'),
				'url' => $item['url']
			);
		}

		return $actions;
	}

 	function getNewActions()
	{
	    $append_actions = array();

		$method = new ObjectCreateNewWebMethod($this->getObject());
		if ( $method->hasAccess() )
		{
			$method->setRedirectUrl('donothing');
			$parms = array (
					'area' => $this->getPage()->getArea()
			);
				
		    if ( $this->getReportBase() == 'mytasks' ) {
		    	$parms['Assignee'] = getSession()->getUserIt()->getId();
		    }

		    $filterValues = $this->getFilterValues();
		    if ( is_numeric($filterValues['iteration']) && $filterValues['iteration'] > 0 ) {
                $parms['Release'] = $filterValues['iteration'];
            }

		    $uid = 'append-task';
			$append_actions[$uid] = array ( 
    			'name' => $method->getObject()->getDisplayName(),
				'uid' => $uid,
				'url' => $method->getJSCall($parms)
			);
		}

		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Iteration'));
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() && $method->hasAccess() )
		{
			$method->setRedirectUrl('donothing');
			$append_actions[] = array(
					'name' => translate('Итерация'),
					'url' => $method->getJSCall() 
			);
		}
		
		return $append_actions;
	}

    function getSortFields()
	{
		$cols = parent::getSortFields();
		array_push( $cols, 'OrderNum');
		return $cols;
	}

	protected function buildAssigneeWorkload( $iterator )
	{
		$object = getFactory()->getObject(get_class($this->getObject()));
		$object->setRegistry(new ObjectRegistrySQL($object));
		$object->addFilter( new FilterVpdPredicate() );
		$object->addFilter( new FilterInPredicate($iterator->idsToArray()) );

		// cache aggregates on workload and spent time
		$planned_aggregate = new AggregateBase( 'Assignee', 'Planned', 'SUM' );
		$object->addAggregate( $planned_aggregate );

		$left_aggregate = new AggregateBase( 'Assignee', 'LeftWork', 'SUM' );
		$object->addAggregate( $left_aggregate );

		$fact_aggregate = new AggregateBase( 'Assignee', 'Fact', 'SUM' );
		$object->addAggregate( $fact_aggregate );
		
		$task_it = $object->getAggregated();

		while( !$task_it->end() )
		{
			$value = $task_it->get($planned_aggregate->getAggregateAlias());
			if ( $value == '' ) $value = 0;
			
			$this->workload[$task_it->get('Assignee')]['Planned'] = $value;
			
			$value = $task_it->get($left_aggregate->getAggregateAlias());
			if ( $value == '' ) $value = 0;
			
			$this->workload[$task_it->get('Assignee')]['LeftWork'] = $value;

			$value = $task_it->get($fact_aggregate->getAggregateAlias());
			if ( $value == '' ) $value = 0;
							
			$this->workload[$task_it->get('Assignee')]['Fact'] = $value;
			
			$task_it->moveNext();
		}
	}

	function buildFiltersName()
	{
		return md5($_REQUEST['report'].md5(strtolower('TaskBoardTable')));
	}

	function drawGroup( $group_field, $object_it )
	{
		switch ( $group_field )
		{
			case 'Release':
				$iteration_it = $this->getListRef()->getGroupIt();
				$iteration_it->moveToId($object_it->get($group_field));

				if ( $iteration_it->getId() > 0 ) {
					list( $capacity, $maximum, $actual_velocity, $estimation ) = $iteration_it->getRealBurndownMetrics();

					echo ' &nbsp; &nbsp; &nbsp; &nbsp; ';
					echo sprintf(
						getSession()->getProjectIt()->IsPortfolio() || !getSession()->getProjectIt()->getMethodologyIt()->IsAgile() ? text(2076) : text(2053),
						$iteration_it->getDateFormatShort('StartDate'),
						$iteration_it->get('FinishDate') == '' ? '?' : $iteration_it->getDateFormatShort('FinishDate'),
                        $maximum > 0 ? $this->estimation_strategy->getDimensionText(round($maximum, 1)) : '0',
						$estimation > $maximum ? 'label label-important' : ($maximum > 0 && $estimation < $maximum ? 'label label-success': ''),
                        $estimation > 0 ? $this->estimation_strategy->getDimensionText(round($estimation, 1)) : '0'
					);
				}
				break;

            case 'Project':
                echo $this->getRenderView()->render('pm/RowGroupActions.php', array(
                    'actions' => $this->getNewCardActions($object_it)
                ));
                break;
		}
	}

	function drawCell( $object_it, $attr )
	{
		switch($attr)
		{
			case 'IssueTraces':
                $objects = preg_split('/,/', $object_it->get($attr));
                $uids = array();

                foreach( $objects as $object_info )
                {
                    list($class, $id, $baseline) = preg_split('/:/',$object_info);
                    if ( $class == '' ) continue;

                    $uid = $this->getUidService();
                    $uid->setBaseline($baseline);

                    $ref_it = $this->getTraces($class);
                    $ref_it->moveToId($id);
                    if ( $ref_it->getId() == '' ) continue;

                    $uids[] = $uid->getUidIconGlobal($ref_it, false) .
                        '<span class="ref-name">' . $ref_it->getDisplayNameExt() . '</span>';
                }

                echo '<span class="tracing-ref">';
                    echo '<span>'.join('</span> <span>',$uids).'</span>';
                echo '</span>';
				break;
		}
	}

	function getRenderParms( $parms )
	{
		$parms = parent::getRenderParms($parms);

		$list = $this->getListRef();
		if ( $list->getGroup() == 'Assignee' ) {
			$iterator = $_REQUEST['tableonly'] == 'true'
				? $this->getObject()->getRegistry()->Query($this->getFilterPredicates())
				: $list->getIteratorRef();
			$this->buildAssigneeWorkload($iterator);
		}

		return $parms;
	}

	function getNewCardActions( $object_it )
	{
		$append_actions = array();
		$filter_values = $this->getFilterValues();

		$method = new ObjectCreateNewWebMethod($this->getObject());
		$method->setRedirectUrl('donothing');
        $method->setVpd($object_it->get('VPD'));

		$parms = array();
		if ( $filter_values['group'] != '' ) {
			$parms[$filter_values['group']] = $object_it->get($filter_values['group']);
		}

		$uid = 'append-task';
		$append_actions[$uid] = array (
			'name' => $this->object->getDisplayName(),
			'uid' => $uid,
			'url' => $method->getJSCall($parms)
		);
		return $append_actions;
	}

	function getDefaultRowsOnPage() {
		return 60;
	}

    function getDetails()
    {
        $values = $this->getFilterValues();
        $userFilter = $this->getFilterUsers($values['taskassignee'], $values);

        $details = parent::getDetails();
        if ( !getFactory()->getAccessPolicy()->can_read(getFactory()->getObject('Participant')) ) {
            return $details;
        }

        return array_merge(
            array_slice($details, 0, 1),
            array (
                'workload' => array (
                    'image' => 'icon-user',
                    'title' => text(716),
                    'url' => getSession()->getApplicationUrl().'details/workload?tableonly=true&users='.$userFilter
                ),
            ),
            array_slice($details, 1)
        );
    }

    function getDetailsParms() {
        return array (
            'active' => 'workload'
        );
    }

    protected function getFamilyModules( $module )
    {
        switch( $module ) {
            case 'tasks-list':
                return array (
                    'tasks-board',
                    'issues-board',
                    'tasks-trace',
                    'issues-list',
                    'project-plan-hierarchy'
                );
            case 'tasks-board':
                return array (
                    'tasks-list',
                    'tasks-trace',
                    'issues-board',
                    'issues-list',
                    'project-plan-hierarchy'
                );
            case 'tasks-trace':
                return array (
                    'tasks-list',
                    'tasks-board',
                    'issues-board',
                    'issues-list',
                    'project-plan-hierarchy'
                );
            default:
                return parent::getFamilyModules($module);
        }
    }

    protected function getChartModules( $module )
    {
        return array(
            'tasks-chart',
            'workitemchart'
        );
    }

    protected function getChartsModuleName()
    {
        return 'tasks-chart';
    }
}
