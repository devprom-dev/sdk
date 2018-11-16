<?php
include_once SERVER_ROOT_PATH."pm/methods/c_date_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/MakeSnapshotWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/StateExFilterWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/FilterStateTransitionMethod.php";
include_once SERVER_ROOT_PATH."pm/views/plan/FilterReleaseMethod.php";
include_once SERVER_ROOT_PATH."pm/views/plan/FilterIterationMethod.php";
include_once SERVER_ROOT_PATH."pm/classes/model/predicates/SinceNotificationFilter.php";
include_once SERVER_ROOT_PATH.'pm/classes/wiki/converters/WikiConverter.php';

include "RequestList.php";
include "RequestChart.php";
include "RequestBoard.php";
include "RequestBoardPlanning.php";
include "RequestTraceList.php";
include "RequestProjectBurnUpChart.php";
include "RequestReleaseBurndownChart.php";

class RequestTable extends PMPageTable
{
 	var $view_filter;
	private $estimation_strategy = null;

 	function __construct( & $object )
 	{
		$this->estimation_strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
		parent::__construct( $object );
 	}
	
	function getList( $mode = '' )
	{
		switch ( $mode )
		{
			case 'trace':
				return new RequestTraceList( $this->getObject() );

			case 'board':
				switch($this->getPage()->getReportBase()) {
					case 'releaseplanningboard':
					case 'iterationplanningboard':
						return new RequestBoardPlanning( $this->getObject() );
					default:
						return new RequestBoard( $this->getObject() );
				}

			case 'chart':
                switch($this->getPage()->getReportBase()) {
                    case 'releaseburndown':
                        return new RequestReleaseBurndownChart( $this->getObject() );
                    case 'projectburnup':
                        return new RequestProjectBurnUpChart( $this->getObject() );
                    default:
                        return new RequestChart( $this->getObject() );
                }

			default:
				return new RequestList( $this->getObject() );
		}
	}

	function getShortCaption() 
	{
		return $this->getCaption();
	}
	
	function hasCrossProjectFilter()
	{
	    if ( getSession()->getProjectIt()->IsPortfolio() ) return true;
        if ( $this->getReportBase() == 'releaseplanningboard' ) return true;
		return parent::hasCrossProjectFilter();
	}
	
	function getActions()
	{
		$actions = array();

    	$method = new BoardExportWebMethod();
		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->url( 'IteratorExportIssueBoard' ) ) );

		$module = getFactory()->getObject('Module');

		$module_it = $module->getExact('attachments');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$item = $module_it->buildMenuItem('class=request');
			if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
			$actions[] = array(
					'name' => text(1373),
					'url' => $item['url']
			);
		}

		$importActions = array();
        $method = new ObjectCreateNewWebMethod($this->getObject());
        if ( $method->hasAccess() ) {
            $method->setRedirectUrl("donothing");

            $module_it = $module->getExact('issues-import');
            if ( getFactory()->getAccessPolicy()->can_read($module_it) && !getSession()->getProjectIt()->IsPortfolio() )
            {
                $item = $module_it->buildMenuItem('?view=import&mode=xml&object=request');
                $importActions[] = array(
                    'name' => text(2280),
                    'url' => $item['url'],
                    'uid' => 'import-excel'
                );
            }

            $importActions['import-doc'] = array(
                'name' => text(2281),
                'url' => $method->getJSCall(array('view' => 'importdoc'), text(2281)),
                'uid' => 'import-doc'
            );
        }

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('ProjectPage'));
        if ( $method->hasAccess() ) {
            $actions[] = array();
            $actions[] = array(
                'name' => text(2511),
                'url' => $method->getJSCall(
                    array(
                        'Request' => 'getCheckedRows'
                    )
                )
            );
        }

        if ( count($importActions) > 0 ) {
            if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
            $actions = array_merge($actions, $importActions);
        }

		return array_merge($actions, parent::getActions());
	}

	function getNewActions()
	{
	    $append_actions = array();
		$group = $this->getListRef()->getGroup();

		if ( $group != 'Project' && getFactory()->getAccessPolicy()->can_create($this->getObject()) ) {
			$append_actions = $this->getNewCardActions(getSession()->getProjectIt());
		}

		if ( in_array($group, array('Function', 'PlannedRelease', 'Iteration')) )
		{
			$method = new ObjectCreateNewWebMethod(
					$group == 'Function' && getSession()->getProjectIt()->getMethodologyIt()->HasFeatures()
						? getFactory()->getObject('Feature')
						: ($group == 'Iteration'
								? getFactory()->getObject('Iteration')
								: getFactory()->getObject('Release'))
			);
			if ( $method->hasAccess() )
			{
				$append_actions[] = array (
				    'uid' => 'new-'.strtolower($group),
					'name' => $method->getObject()->getDisplayName(), 
                    'url' => $method->getJSCall(
								array (
	                   					'area' => $this->getPage()->getArea()
	                   			)
	                   		 ) 
				);
			}
		}
		
		return $append_actions;
	}

	function getExportActions()
    {
        $actions = parent::getExportActions();

        $method = new WikiExportBaseWebMethod();
        $methodPageIt = $this->getObject()->createCachedIterator(
            array (
                array ('pm_ChangeRequestId' => '%ids%')
            )
        );
        $converter = new WikiConverter( $this->getObject() );
        $converter_it = $converter->getAll();
        while( !$converter_it->end() ) {
            $actions[] = array(
                'name' => $converter_it->get('Caption'),
                'url' => $method->url($methodPageIt, $converter_it->get('EngineClassName'))
            );
            $converter_it->moveNext();
        }

        return $actions;
    }

    function getNewCardActions( $project_it )
	{
		$method = new ObjectCreateNewWebMethod($this->getObject());
		if ( !$method->hasAccess() ) return array();

		$method->setRedirectUrl('donothing');
        $method->setVpd($project_it->get('VPD'));
		
		$parms = array (
				'area' => $this->getPage()->getArea()
		);
        $append_actions = array();
        $filter_values = $this->getFilterValues();

		$type_it = getFactory()->getObject('pm_IssueType')->getRegistry()->Query(
            array (
                new FilterVpdPredicate($project_it->get('VPD')),
                in_array($filter_values['type'], array('none',''))
                    ? new FilterAttributeNullPredicate('ReferenceName')
                    : new FilterAttributePredicate('ReferenceName', $filter_values['type']),
                new SortOrderedClause()
            )
		);
		while ( !$type_it->end() )
		{
			$parms['Type'] = $type_it->getId();
			$uid = $type_it->get('ReferenceName') == '' ? 'new-issue' : 'new-issue-'.$type_it->get('ReferenceName');
			
			$append_actions[$uid] = array ( 
				'name' => $type_it->getDisplayName(),
				'uid' => $uid,
				'url' => $method->getJSCall($parms, $type_it->getDisplayName())
			);
			
			$type_it->moveNext();
		}
		
		unset($parms['Type']);
		
		$template_it = getFactory()->getObject('RequestTemplate')->getRegistry()->Query(
				array ( new FilterVpdPredicate($project_it->get('VPD')) )
		);
		if ( $template_it->count() > 0 ) $append_actions[] = array();
		
		while( !$template_it->end() )
		{
			$parms['template'] = $template_it->getId();
			$append_actions[] = array ( 
					'name' => $template_it->getDisplayName(),
					'url' => $method->getJSCall($parms)
			);
			
			$template_it->moveNext();
		}
		
		return $append_actions;
	}
	
	function getViewFilter()
	{
		if ( !is_object($this->view_filter) )
		{
			$this->view_filter = new ViewRequestListViewWebMethod(); 
		}
		
		return $this->view_filter;
	}
	
 	function getSortFields()
	{
		$cols = parent::getSortFields();
		array_push( $cols, 'OrderNum');

		unset($cols[array_search('Watchers', $cols)]);
		unset($cols[array_search('Attachment', $cols)]);
		unset($cols[array_search('Links', $cols)]);
		unset($cols[array_search('Spent', $cols)]);
		
		return $cols;
	}
	
    function getSortAttributeClause( $field )
	{
	    $parts = preg_split('/\./', $field);
        switch($parts[0]) {
            case 'Owner':
                return new IssueOwnerSortClause();
            case 'Function':
                return new IssueFunctionSortClause();
            case 'Type':
            case 'TypeBase':
                return new IssueUnifiedTypeSortClause();
            default:
                return parent::getSortAttributeClause( $field );
        }
	}
	
 	function getFilterActions()
	{
	    $actions = parent::getFilterActions();
	    
	    if ( in_array($this->getReportBase(), array('releaseburndown', 'projectburnup')) )
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
	    if ( in_array($this->getReportBase(), array('releaseburndown')) ) {
	        return $this->getFiltersReleaseBurndown();
	    }
        if ( in_array($this->getReportBase(), array('projectburnup')) ) {
            return array();
        }

	    return $this->getFiltersBase();
	}
	
	function getFiltersReleaseBurndown()
	{
		$release = getFactory()->getObject('Release');
		$release->addFilter( new ReleaseTimelinePredicate('current') );
		$releases = new FilterObjectMethod( $release, translate('Релизы'), 'release', false);
		$releases->setType( 'singlevalue' );
		$releases->setHasNone(false);
		
		return array( $releases );
	}
	
	function getFiltersBase()
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		// build Responsible filter
		
		$filters = array (
			$this->buildFilterState(),
			$this->buildFilterPriority(),
			new ViewSubmmitedAfterDateWebMethod(),
			new ViewSubmmitedBeforeDateWebMethod(),
            new ViewModifiedAfterDateWebMethod(),
			new ViewModifiedBeforeDateWebMethod(),
            new FilterDateWebMethod(text(2539), 'finishedafter'),
            new FilterDateWebMethod(text(2538), 'finishedbefore'),
			$this->buildFilterOwner(),
			$this->buildTagsFilter(),
			$this->buildFilterAuthor(),
			$this->buildFilterTaskType(),
			new ViewRequestTaskStateWebMethod()
		);

		if ( class_exists(getFactory()->getClass('Severity')) ) {
            $filters[] = $this->buildFilterSeverity();
        }

		$filter = new FilterObjectMethod( getFactory()->getObject('Version'), translate('Выполнено в версии'), 'version');
		$filter->setIdFieldName('Caption');
		$filters[] = $filter;


		if ( getFactory()->getObject('RequestType')->getAll()->count() > 0 ) {
			$filters[] = $this->buildFilterType();
		}
			
		if ( $methodology_it->HasFeatures() ) {
			$filters[] = $this->buildFilterFunction();
		}
		
		if ( $methodology_it->HasReleases() ) {
			$filters = array_merge(
			    array_slice($filters, 0, 1), array( new FilterReleaseMethod() ), array_slice($filters, 1)
            );
		}
		
		if ( $methodology_it->HasPlanning() ) {
			$filters[] = new FilterIterationMethod();
		}

		$filter = $this->buildUserGroupFilter();
		if ( is_object($filter) ) $filters[] = $filter;
		$filters[] = $this->buildUserRoleFilter();

		$filter = $this->buildFilterEstimation();
		if ( is_object($filter) ) $filters[] = $filter;

		array_push( $filters, $this->buildFilterSubmittedVersion() );

	    $filter = $this->buildSnapshotFilter();
	    if ( is_object($filter) ) $filters[] = $filter;

        $filters[] = $this->buildDeadlineFilter();

		return array_merge( $filters, parent::getFilters() );
	}
	
 	function getFilterPredicates()
	{
 		$values = $this->getFilterValues();
        $this->parseFilterValues($values);

 		$predicates = array();
 		
		$predicates[] = new StatePredicate( $values['state'] );
		$predicates[] = new FilterAttributePredicate('Priority', $values['priority']);
        $predicates[] = new FilterAttributePredicate('Severity', $values['severity']);
		$predicates[] = $this->buildOwnerPredicate($values);
		$predicates[] = new FilterAttributePredicate('Type', $values['type']);
		$predicates[] = new FilterSubmittedAfterPredicate($values['submittedon']);
		$predicates[] = new FilterSubmittedBeforePredicate($values['submittedbefore']);
		$predicates[] = new FilterAttributePredicate('SubmittedVersion',$values['subversion']);
		$predicates[] = new RequestFeatureFilter($values['function']);
		$predicates[] = new RequestTagFilter($values['tag']);
		$predicates[] = new RequestReleasePredicate($values['release']);
		$predicates[] = new RequestReleasePredicate($_REQUEST['plannedrelease']);
		$predicates[] = new RequestTestResultPredicate($_REQUEST['test']);
		$predicates[] = new RequestAuthorFilter( $values['author'] );
		$predicates[] = new TransitionObjectPredicate( $this->getObject(), $values['transition'] );
		$predicates[] = new RequestTaskTypePredicate( $values['tasktype'] );
		$predicates[] = new RequestTaskStatePredicate( $values['taskstate'] );
		$predicates[] = new FilterAttributePredicate('ClosedInVersion', $values['version']);
		$predicates[] = new FilterAttributePredicate('Iteration',$values['iteration']);
		$predicates[] = new FilterAttributePredicate('Iteration',$_REQUEST['iterations']);
        $predicates[] = new FilterDateAfterPredicate('FinishDate', $values['finishedafter']);
        $predicates[] = new FilterDateBeforePredicate('FinishDate', $values['finishedbefore']);

		$trace = getFactory()->getObject('pm_ChangeRequestTrace');
		array_push($predicates, new RequestTracePredicate( $_REQUEST['trace'] ) );

		$predicates[] = new FilterModifiedAfterPredicate($values['modifiedafter']);
		$predicates[] = new FilterModifiedBeforePredicate($values['modifiedbefore']);
		$predicates[] = new RequestEstimationFilter($values['estimation']);
        $predicates[] = $this->buildDeadlinePredicate($values);

        if ( $this->getPage()->getReportBase() == 'sincelastview' ) {
            $predicates[] = new SinceNotificationFilter(getSession()->getUserIt());
        }

        $projectIt = getSession()->getProjectIt();
        $methodology_it = $projectIt->getMethodologyIt();
        if ( $methodology_it->get('IsRequirements') == ReqManagementModeRegistry::RDD ) {
            $predicates[] = new FilterAttributeNotNullPredicate('Type');
        }

		return array_merge($predicates, parent::getFilterPredicates());
	}

    function buildTagsFilter()
    {
        $tag = getFactory()->getObject('RequestTag');
        $filter = new FilterObjectMethod($tag, translate('Тэги'), 'tag');
        $filter->setIdFieldName('Tag');
        return $filter;
    }

	protected function buildFilterEstimation()
	{
		$scale = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy()->getFilterScale();
		if ( count($scale) < 1 ) return;
		return new ViewRequestEstimationWebMethod($scale);
	}

	protected function buildFilterType()
	{
		$type_method = new FilterObjectMethod( getFactory()->getObject('RequestType'), translate('Тип'), 'type');
		$type_method->setIdFieldName( 'ReferenceName' );
		if ( $this->getObject()->IsAttributeRequired('Type') ) {
            $type_method->setHasNone(false);
        }
        else {
            $type_method->setNoneTitle( $this->getObject()->getDisplayName() );
        }
		return $type_method;
	}
	
	protected function buildFilterOwner() {
		return new FilterObjectMethod( getFactory()->getObject('ProjectUser'), translate($this->getObject()->getAttributeUserName('Owner')), 'owner' );
	}
	
	protected function buildFilterAuthor() {
		return new FilterObjectMethod(getFactory()->getObject('IssueAuthor'), translate('Автор'), 'author');
	}

	protected function buildFilterFunction()
	{
		$filter = new FilterObjectMethod(getFactory()->getObject('Feature'), '', 'function');
		return $filter;
	}

    protected function buildFilterTaskType()
    {
        $filter = new FilterObjectMethod(getFactory()->getObject('TaskType'), text(1107), 'tasktype');
        $filter->setIdFieldName('ReferenceName');
        $filter->setHasNone(false);
        return $filter;
    }

	protected function buildFilterPriority()
	{
		$priority = getFactory()->getObject('Priority');
		$filter = new FilterObjectMethod($priority);
		$filter->setHasNone(false);
		return $filter;
	}

    protected function buildFilterSeverity()
    {
        $priority = getFactory()->getObject('Severity');
        $filter = new FilterObjectMethod($priority);
        return $filter;
    }

	protected function buildFilterSubmittedVersion()
	{
		$filter = new FilterObjectMethod(getFactory()->getObject('Version'), translate('Обнаружено в версии'), 'subversion');
		return $filter;
	}

    protected function buildDeadlineFilter()
    {
        return new FilterDateWebMethod(translate('Завершить к'), 'deliverydate');
    }

	function getFeatureTitle( $feature_it, $object_it, $uid )
	{
		if ( $object_it->get('Function') == '' ) {
			return '';
		}
		else {
			$feature_it->moveToId($object_it->get('Function'));
					
			$parents = $feature_it->getParentsArray();
			if ( count($parents) > 1 ) {
				$parent_it = $feature_it->object->getExact($parents);
				$titles = array();
				while( !$parent_it->end() ) {
					$titles[$parent_it->get('SortIndex')] = $parent_it->getDisplayName();
					$parent_it->moveNext();  
				}
				ksort($titles);
				return translate($object_it->object->getAttributeUserName('Function')).': '.
						$uid->getUidIconGlobal($feature_it).' '.join(' / ', $titles);
			}
		}
		return '';
	}

	function drawGroup( $group_field, $object_it )
	{
		switch ( $group_field )
		{
			case 'PlannedRelease':
				echo ' &nbsp; &nbsp; &nbsp; &nbsp; ';

				$release_it = $this->getListRef()->getGroupIt();
				$release_it->moveToId($object_it->get($group_field));

				if ( $release_it->getId() > 0 ) {
					list( $capacity, $maximum, $actual_velocity, $estimation ) = $release_it->getRealBurndownMetrics();
					echo sprintf(
						getSession()->getProjectIt()->IsPortfolio() || !getSession()->getProjectIt()->getMethodologyIt()->IsAgile() ? text(2076) : text(2053),
						$release_it->getDateFormatShort('StartDate'),
						$release_it->get('FinishDate') == '' ? '?' : $release_it->getDateFormatShort('FinishDate'),
                        $maximum > 0 ? $this->estimation_strategy->getDimensionText(round($maximum, 1)) : '0',
						$estimation > $maximum ? 'label label-important' : ($maximum > 0 && $estimation < $maximum ? 'label label-success': ''),
                        $estimation > 0 ? $this->estimation_strategy->getDimensionText(round($estimation, 1)) : '0'
					);
				}
				break;

			case 'Iteration':
				echo ' &nbsp; &nbsp; &nbsp; &nbsp; ';

				$release_it = $this->getListRef()->getGroupIt();
				$release_it->moveToId($object_it->get($group_field));

				if ( $release_it->getId() > 0 ) {
					list( $capacity, $maximum, $actual_velocity, $estimation ) = $release_it->getRealBurndownMetrics();
					echo sprintf(
                        getSession()->getProjectIt()->IsPortfolio() || !getSession()->getProjectIt()->getMethodologyIt()->IsAgile() ? text(2076) : text(2053),
                        $release_it->getDateFormatShort('StartDate'),
                        $release_it->get('FinishDate') == '' ? '?' : $release_it->getDateFormatShort('FinishDate'),
                        $maximum > 0 ? $this->estimation_strategy->getDimensionText(round($maximum, 1)) : '0',
                        $estimation > $maximum ? 'label label-important' : ($maximum > 0 && $estimation < $maximum ? 'label label-success': ''),
                        $estimation > 0 ? $this->estimation_strategy->getDimensionText(round($estimation, 1)) : '0'
					);
				}
				break;

			case 'Project':
				$project_it = $this->getListRef()->getGroupIt();
				$project_it->moveToId($object_it->get($group_field));
				echo $this->getRenderView()->render('pm/RowGroupActions.php', array (
					'actions' => $this->getNewCardActions($project_it)
				));
				break;
		}
	}

	function getAssigneeWorkload() {
		return $this->workload;
	}

	protected function buildOwnerPredicate( $values ) {
        return new FilterAttributePredicate( 'Owner', $this->getFilterUsers($values['owner'],$values) );
    }

    function buildDeadlinePredicate( $values ) {
        return new FilterDateBeforePredicate('DeliveryDate', $values['deliverydate']);
    }

	protected function buildAssigneeWorkload( $iterator )
	{
		$object = getFactory()->getObject(get_class($this->getObject()));
		$object->setRegistry(new ObjectRegistrySQL($object));
		$object->addFilter( new FilterInPredicate($iterator->idsToArray()) );

		// cache aggregates on workload and spent time
		$planned_aggregate = new AggregateBase( 'Owner', 'Estimation', 'SUM' );
		$object->addAggregate( $planned_aggregate );

		$left_aggregate = new AggregateBase( 'Owner', 'EstimationLeft', 'SUM' );
		$object->addAggregate( $left_aggregate );

		$fact_aggregate = new AggregateBase( 'Owner', 'Fact', 'SUM' );
		$object->addAggregate( $fact_aggregate );

		$object_it = $object->getAggregated();
		while( !$object_it->end() )
		{
			$value = $object_it->get($planned_aggregate->getAggregateAlias());
			if ( $value == '' ) $value = 0;
			$this->workload[$object_it->get('Owner')]['Planned'] = $value;

			$value = $object_it->get($left_aggregate->getAggregateAlias());
			if ( $value == '' ) $value = 0;
			$this->workload[$object_it->get('Owner')]['LeftWork'] = $value;

			$value = $object_it->get($fact_aggregate->getAggregateAlias());
			if ( $value == '' ) $value = 0;
			$this->workload[$object_it->get('Owner')]['Fact'] = $value;

			$object_it->moveNext();
		}
	}

    function getDetails()
    {
        $values = $this->getFilterValues();
        $userFilter = $this->getFilterUsers($values['owner'], $values);

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
        if ( in_array($this->getReportBase(), array('iterationplanningboard','releaseplanningboard')) ) {
            return array (
                'active' => 'props'
            );
        }
        else {
            return array (
                'active' => $_REQUEST['view'] == 'board' ? 'workload' : 'props'
            );
        }
    }

    function getRenderParms( $parms )
	{
		$parms = parent::getRenderParms($parms);

		$list = $this->getListRef();
		if ( $list->getGroup() == 'Owner' ) {
			$iterator = $_REQUEST['tableonly'] == 'true'
					? $this->getObject()->getRegistry()->Query($this->getFilterPredicates())
					: $list->getIteratorRef();

			$this->buildAssigneeWorkload($iterator);
		}

		return $parms;
	}

    protected function getFamilyModules( $module )
    {
        $taskReports = array(
            'mytasks',
            'nearesttasks',
            'assignedtasks',
            'newtasks',
            'issuesmine',
            'watchedtasks',
            'project-plan-hierarchy',
            'customs/workflowanalysis',
            'tasks-board'
        );
        switch( $module ) {
            case 'kanban/requests':
                return array_merge(
                        array (
                        'issues-board',
                        'issues-backlog',
                        'issues-trace'
                    ),
                    $taskReports
                );
            case 'issues-backlog':
                return array_merge(
                    array (
                        'issues-board',
                        'kanban/requests',
                        'issues-trace'
                    ),
                    $taskReports
                );
            case 'issues-board':
                return array_merge(
                    array (
                        'issues-backlog',
                        'kanban/requests',
                        'issues-trace'
                    ),
                    $taskReports
                );
            case 'issues-trace':
                return array (
                    'issues-backlog',
                    'kanban/requests',
                    'issues-board'
                );
            default:
                return parent::getFamilyModules($module);
        }
    }

    protected function getChartModules( $module )
    {
        return array(
            'issues-chart',
            'workitemchart'
        );
    }

    protected function getChartsModuleName()
    {
        return 'issues-chart';
    }

    function getDefaultRowsOnPage() {
		return 60;
	}
}