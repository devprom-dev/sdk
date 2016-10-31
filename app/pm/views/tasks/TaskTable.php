<?php

include "TaskList.php";
include "TaskTraceList.php";
include "TaskChart.php";
include "TaskBoardList.php";
include "TasktBoardPlanning.php";
include "IteratorExportTaskBoard.php";
include "TaskPlanFactChart.php";
include_once SERVER_ROOT_PATH.'pm/methods/c_task_methods.php';
include_once SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';
include_once SERVER_ROOT_PATH.'pm/methods/StateExFilterWebMethod.php';
include_once SERVER_ROOT_PATH."pm/methods/FilterStateTransitionMethod.php";

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
						return new TaskPlanFactChart( $this->getObject() );
					default:
						return new TaskChart( $this->getObject() );
				}

			default:
				switch($this->getPage()->getReportBase()) {
					case 'tasksplanningboard':
						return new TasktBoardPlanning( $this->getObject() );
					default:
						return new TaskBoardList( $this->getObject(), $this->is_finished );
				}
		}
	}
	
	function hasCrossProjectFilter()
	{
        if ( getSession()->getProjectIt()->IsPortfolio() ) return true;
		if ( $this->getReportBase() == 'tasksboardcrossproject' ) return true;
		if ( $_REQUEST['view'] == 'board' ) return false;

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
	
	function getFilterActions()
	{
	    $actions = parent::getFilterActions();
	     
	    if ( $this->getReportBase() == 'iterationburndown' )
	    {
            foreach( $actions as $key => $action )
            {
                if ( $action['id'] == 'save' ) continue;
                
                unset($actions[$key]);
            }
	    }
	    
	    return $actions;
	}
	
 	function getFilters()
	{
	    if ( $this->getReportBase() == 'iterationburndown' )
	    {
	        return $this->getFiltersIterationBurndown();
	    }
	    
	    return $this->getFiltersBase();
	}
	
	function getFiltersIterationBurndown()
	{
	    $iteration = getFactory()->getObject('Iteration');
 		$iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
		$iterations = new FilterObjectMethod($iteration, translate('Итерация'), 'release', false);
		$iterations->setType('singlevalue');
		$iterations->setHasNone(false);
		
		return array( $iterations );
	}
		
 	function getFiltersBase()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		$filters = array(
			$this->buildStateFilter(),
            $this->buildIterationFilter(),
			$this->buildTypeFilter(),
			new FilterObjectMethod( getFactory()->getObject('Priority'), '', 'taskpriority' ),
			$this->buildAssigneeFilter(),
			new ViewSubmmitedAfterDateWebMethod(),
			new ViewSubmmitedBeforeDateWebMethod(),
			new ViewModifiedBeforeDateWebMethod(),
			new ViewModifiedAfterDateWebMethod(),
			new FilterAutoCompleteWebMethod(getFactory()->getObject('Request'), '', 'issue')
		);

		$filter = $this->buildUserGroupFilter();
		if ( is_object($filter) ) $filters[] = $filter;
		$filters[] = $this->buildUserRoleFilter();

		if ( $methodology_it->HasFeatures() ) {
			$filters[] = $this->buildFilterFunction();
		}

		return array_merge( $filters, PMPageTable::getFilters() ); 		
	}

	protected function buildTypeFilter()
	{
	    $taskType = getFactory()->getObject('pm_TaskType');
		$type_method = new FilterObjectMethod( $taskType, translate('Тип'), 'tasktype');
		$type_method->setIdFieldName( 'ReferenceName' );
		return $type_method;
	}

	protected function buildStateFilter()
	{
		if ( getSession()->getProjectIt()->IsPortfolio() )
		{
			$metastate = getFactory()->getObject('StateMeta');
	 		$metastate->setAggregatedStateObject(getFactory()->getObject('TaskState'));
	 		$state_it = $metastate->getRegistry()->getAll();
		} 
		else {
			$state_it = WorkflowScheme::Instance()->getStateIt($this->getObject());
		}
		return new StateExFilterWebMethod($state_it, 'taskstate');
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
	
	protected function buildIterationFilter()
	{
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() )
		{
			$release = getFactory()->getObject('Release');
	 		$release->addFilter( new ReleaseTimelinePredicate('not-passed') );
			return new FilterObjectMethod($release, translate('Релиз'), 'issue-release');
		}
		else
		{ 
			$iteration = getFactory()->getObject('Iteration');
	 		$iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
			return new FilterObjectMethod($iteration, translate('Итерация'), 'iteration');
		}
	}

	protected function buildFilterFunction()
	{
		$filter = new FilterObjectMethod(getFactory()->getObject('Feature'), '', 'function');
		return $filter;
	}

 	function getFilterPredicates()
	{
		global $_REQUEST;
		
		$values = $this->getFilterValues();
		
		$predicates = array(
			$this->buildStatePredicate($values['taskstate']),
			new FilterAttributePredicate( 'Priority', $values['taskpriority'] ),
			new FilterAttributePredicate( 'TaskType', $values['tasktype'] ),
			new FilterAttributePredicate( 'Assignee', $this->getFilterUsers($values['taskassignee'],$values) ),
			new FilterAttributePredicate( 'Release', $values['iteration'] ),
			new TaskReleasePredicate($values['issue-release']),
 			new TaskVersionPredicate( $values['stage'] ),
			new FilterSubmittedAfterPredicate( $values['submittedon'] ),
			new FilterSubmittedBeforePredicate( $values['submittedbefore'] ),
			new FilterAttributePredicate( 'ChangeRequest', $_REQUEST['issue'] ),
			new TaskFeaturePredicate($_REQUEST['function']),
            new TaskIssueStatePredicate($_REQUEST['issueState'])
		);		

		$predicates[] = new FilterModifiedAfterPredicate($values['modifiedafter']);
		$predicates[] = new FilterModifiedBeforePredicate($values['modifiedbefore']);
 		$predicates[] = new TaskBindedToObjectPredicate($_REQUEST['trace']);

		return array_merge(parent::getFilterPredicates(), $predicates);
	}

	function buildStatePredicate( $value ) {
		return new StatePredicate( $value );
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
				
		    if ( $this->getReportBase() == 'mytasks' )
		    {
		    	$parms['Assignee'] = getSession()->getUserIt()->getId();
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

		if ( getSession()->getProjectIt()->getMethodologyIt()->get('IsRequestOrderUsed') == 'Y' )
		{
			array_push( $cols, 'OrderNum');
		}
	
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

	function getFiltersName()
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
					$estimation = $iteration_it->getTotalWorkload();
					list( $capacity, $maximum, $actual_velocity ) = $iteration_it->getRealBurndownMetrics();

					echo ' &nbsp; &nbsp; &nbsp; &nbsp; ';
					echo sprintf(
						getSession()->getProjectIt()->IsPortfolio() ? text(2076) : text(2053),
						$iteration_it->getDateFormatShort('StartDate'),
						$iteration_it->get('FinishDate') == '' ? '?' : $iteration_it->getDateFormatShort('FinishDate'),
						$this->estimation_strategy->getDimensionText(round($maximum, 1)),
						$estimation > $maximum ? 'label label-important' : ($maximum > 0 && $estimation < $maximum ? 'label label-success': ''),
						$this->estimation_strategy->getDimensionText(round($estimation, 1))
					);
				}
				break;
		}

		switch ( $group_field ) {
			case 'Project':
				echo $this->getView()->render('pm/RowGroupActions.php', array(
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
					list($class, $id) = preg_split('/:/',$object_info);
					if ( $class == '' ) continue;
					$ref_it = $this->getTraces($class);
					$ref_it->moveToId($id);
					if ( $ref_it->getId() == '' ) continue;
					$uids[] = $this->getUidService()->getUidIcon($ref_it);
				}
				echo join(', ',$uids);
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

		$object = $this->getObject();
		$object->setVpdContext($object_it);

		$method = new ObjectCreateNewWebMethod($object);
		$method->setRedirectUrl('donothing');

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
            'active' => 'props'
        );
    }

    protected function getFamilyModules( $module )
    {
        switch( $module ) {
            case 'tasks-list':
                return array (
                    'tasks-board'
                );
            case 'tasks-board':
                return array (
                    'tasks-list'
                );
            default:
                return parent::getFamilyModules($module);
        }
    }
}