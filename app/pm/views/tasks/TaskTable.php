<?php

include "TaskList.php";
include "TaskTraceList.php";
include "TaskBoardList.php";
include "TaskBoardPlanning.php";
include "IteratorExportTaskBoard.php";
include_once SERVER_ROOT_PATH."pm/views/plan/FilterReleaseMethod.php";
include_once SERVER_ROOT_PATH."pm/views/plan/FilterIterationMethod.php";

class TaskTable extends PMPageTable
{
	private $estimation_strategy = null;
	private $uidService = null;
	private $iteration = null;
	private $workloadReportIt = null;

    function __construct( $object )
    {
        parent::__construct($object);
        $this->estimation_strategy = new EstimationHoursStrategy();
        $this->uidService = new ObjectUID();
        $this->workloadReportIt = getFactory()->getObject('Module')->getExact('resman/resourceload');
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
				switch($this->getReportBase())
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
	    
	    if ( $parts[0] == 'Assignee' ) {
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
			$this->buildFilterPriority()
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
            new FilterDateIntervalWebMethod(translate('Выполнено'), 'finishedafter'),
			new FilterDateIntervalWebMethod(translate('Выполнено'), 'finishedbefore'),
			new FilterReferenceWebMethod(getFactory()->getObject('Request'), '', 'issue')
		));

        if ( $methodology_it->IsTimeTracking() ) {
            $filters[] = new FilterDateIntervalWebMethod(text(2334), 'spentafter');
            $filters[] = new FilterDateIntervalWebMethod(text(2334), 'spentbefore');
        }

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

    protected function buildFilterPriority()
    {
        $priority = getFactory()->getObject('Priority');
        $filter = new FilterObjectMethod($priority, '', 'taskpriority');
        $filter->setHasNone(false);
        $filter->setHasAny(false);
        return $filter;
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
        $filter->setCaption(getSession()->IsRDD() ? text(2128) : text(3015));
        return $filter;
    }
	
	protected function buildAssigneeFilter()
	{
		$user_it = getFactory()->getObject('WorkerUser')->getAll();
		if ( $user_it->count() < 1 ) {
			$user_it = getFactory()->getObject('User')->getRegistry()->Query(
				array( new FilterInPredicate(getSession()->getUserIt()->getId()) )
			);
		}
		return new FilterObjectMethod( $user_it, text(753), 'taskassignee' );
	}

    protected function buildAuthorFilter()
    {
        $user_it = getFactory()->getObject('WorkerUser')->getAll();
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
        }
		if ( !getSession()->getProjectIt()->IsPortfolio() ) {
            if ( $this->getReportBase() == 'iterationburndown' || $this->getPage()->getModule() == 'tasks-board' ) {
                $filter->setDefaultValue(getFactory()->getObject('IterationActual')->getFirst()->getId());
            }
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

 	function getFilterPredicates( $values )
	{
		$predicates = array(
			$this->buildStatePredicate($values['state']),
            $this->buildAssigneePredicate($values),
			$this->buildIssueStatePredicate($values),
			new FilterAttributePredicate( 'Priority', $values['taskpriority'] ),
			new FilterAttributePredicate( 'Release', $values['iteration'] ),
			new TaskReleasePredicate($values['issue-release']),
 			new TaskVersionPredicate( $values['stage'] ),
			new FilterSubmittedAfterPredicate( $values['submittedon'] ),
			new FilterSubmittedBeforePredicate( $values['submittedbefore'] ),
			new FilterDateAfterPredicate('FinishDate', $values['finishedafter']),
            new FilterDateBeforePredicate('FinishDate', $values['finishedbefore']),
			new FilterAttributePredicate( 'ChangeRequest', $_REQUEST['issue'] != '' ? $_REQUEST['issue'] : $values['issue'] ),
			new TaskFeaturePredicate($_REQUEST['function'] != '' ? $_REQUEST['function'] : $values['function']),
            $this->buildTagPredicate($values),
            $this->buildDeadlinePredicate($values),
            new SpentTimeReportDatePredicate($values['spentafter'], $values['spentbefore'])
		);		

		$predicates[] = new FilterModifiedAfterPredicate($values['modifiedafter']);
		$predicates[] = new FilterModifiedBeforePredicate($values['modifiedbefore']);
 		$predicates[] = new TaskBindedToObjectPredicate($_REQUEST['trace']);

        $this->iteration = is_numeric($values['iteration']) ? $values['iteration'] : 0;

		return array_merge(parent::getFilterPredicates( $values ), $predicates);
	}

	function buildTagPredicate($values) {
        return new CustomTagFilter( $this->getObject(), $values['tag'] );
    }

    function buildDeadlinePredicate( $values ) {
	    return new FilterDateBeforePredicate('PlannedFinishDate', $values['plannedfinish']);
    }

	function buildAssigneePredicate( $values ) {
        return new FilterAttributePredicate( 'Assignee', $this->getFilterUsers($values['taskassignee'],$values) );
    }

    function buildIssueStatePredicate( $values ) {
	    return new TaskIssueStatePredicate($_REQUEST['issueState'] != '' ? $_REQUEST['issueState'] : $values['issueState']);
    }

	function getActions()
	{
		$actions = parent::getActions();

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

		return $actions;
	}

 	function getNewActions()
	{
	    $append_actions = array();

		$method = new ObjectCreateNewWebMethod($this->getObject());
		if ( $method->hasAccess() ) {
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

		    $taskTypes = \TextUtils::parseFilterItems($filterValues['tasktype']);
            if ( count($taskTypes) > 0 ) {
                $parms['TaskType'] = array_shift(
                    getFactory()->getObject('TaskType')->getRegistry()->QueryKeys(
                        array(
                            new FilterVpdPredicate(),
                            new FilterAttributePredicate('ReferenceName', $taskTypes)
                        )
                    )->idsToArray()
                );
            }

		    $uid = 'append-task';
			$append_actions[$uid] = array ( 
    			'name' => $method->getObject()->getDisplayName(),
				'uid' => $uid,
				'url' => $method->getJSCall($parms)
			);

			$templateIt = getFactory()->getObject('TaskTemplate')->getAll();
			while( !$templateIt->end() ) {
                $uid = 'append-tasktemplate-'.$templateIt->getId();
                $append_actions[$uid] = array (
                    'name' => $templateIt->getDisplayName(),
                    'uid' => $uid,
                    'url' => $method->getJSCall(array_merge( $parms,
                                array(
                                    'template' => $templateIt->getId()
                                )))
                );
                $templateIt->moveNext();
            }
		}

		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Iteration'));
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() && $method->hasAccess() ) {
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

	function buildFiltersName()
	{
		return md5($this->getReport().md5(strtolower('TaskBoardTable')));
	}

	function drawGroup( $group_field, $object_it )
	{
		switch ( $group_field )
		{
            case 'Project':
                echo $this->getRenderView()->render('pm/RowGroupActions.php', array(
                    'actions' => $this->getNewCardActions($object_it, false)
                ));
                break;

            case 'Assignee':
                $assigneeIt = $object_it->getRef('Assignee');
                echo $this->getRenderView()->render('pm/UserWorkload.php', array (
                    'workload' => $assigneeIt->get('PlannedWorkload'),
                    'date' => $assigneeIt->get('FreeWorkingDate'),
                    'report_url' => $this->workloadReportIt->getId() != ''
                                        ? $this->workloadReportIt->getUrl('taskassignee='.$object_it->get('Assignee'))
                                        : ""
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
        $this->cacheTraces('IssueTraces');
		return $parms;
	}

	function getNewCardActions( $object_it, $doSelectProject = true )
	{
		$append_actions = array();
		$filter_values = $this->getFilterValues();

		$method = new ObjectCreateNewWebMethod($this->getObject());
        $method->setVpd($object_it->get('VPD'));
        if ( !$doSelectProject ) $method->doSelectProject(false);

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

    function getDetailsParms() {
        return array (
            'active' => $_REQUEST['view'] == 'board' ? '' : 'form'
        );
    }

    protected function getFamilyModules( $module )
    {
        return array(
            'tasks-list',
            'tasks-board',
            'issues-board',
            'tasks-trace',
            'issues-backlog',
            'project-plan-hierarchy'
        );
    }

    protected function getChartModules( $module )
    {
        return array(
            'tasks-chart',
            'resman/resourceload'
        );
    }

    protected function getChartsModuleName()
    {
        return 'tasks-chart';
    }
}
