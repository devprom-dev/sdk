<?php

include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedBeforeDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedAfterDateWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/c_date_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/MakeSnapshotWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/StateExFilterWebMethod.php";

include "RequestList.php";
include "RequestChart.php";
include "RequestBoard.php";
include "RequestTraceList.php";

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
				return new RequestBoard( $this->getObject() );

			case 'chart':
				return new RequestChart( $this->getObject() );
				
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
		if ( $this->getReportBase() == 'issuesboardcrossproject' ) return true;
		
		return parent::hasCrossProjectFilter();
	}
	
	function getActions()
	{
		global $model_factory;
		
		$actions = array();
		
		$list = $this->getListRef();
		$it = $list->getIteratorRef();
		
		$method = new ReleaseNotesRequestWebMethod();
		if ( $method->hasAccess() )
		{
			array_push($actions, array( 'name' => $method->getCaption(),
				'url' => $method->getJSCall() ) );
		}
		
		$method = new ExcelExportWebMethod();
		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->getJSCall( $this->getShortCaption() ) ) );
		$actions[] = array();
		
		$method = new BoardExportWebMethod();
		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->getJSCall( 'IteratorExportIssueBoard' ) ) );

		$method = new HtmlExportWebMethod();
		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->getJSCall() ) );

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

		$module_it = $module->getExact('issues-import');
	    if ( getFactory()->getAccessPolicy()->can_read($module_it) )
	    {
        	if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
	        $item = $module_it->buildMenuItem('?view=import&mode=xml&object=request');
		    $actions[] = array(
                'name' => translate('Импортировать'),
				'url' => $item['url']
            );
	    }
	    
	    $trace_attributes = $this->getObject()->getAttributesByGroup('trace');
        if ( count($trace_attributes) > 0 )
        {
            if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
    		$actions['trace'] = array (
    		        'uid' => 'trace', 
    		        'name' => translate('Трассировка'),
    				'items' => array() 
    		);
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

		if ( in_array($group, array('Function', 'PlannedRelease', 'Iterations')) )
		{
			$method = new ObjectCreateNewWebMethod(
					$group == 'Function' && getSession()->getProjectIt()->getMethodologyIt()->HasFeatures()
						? getFactory()->getObject('Feature')
						: ($group == 'Iterations'
								? getFactory()->getObject('Iteration')
								: getFactory()->getObject('Release'))
			);
			if ( $method->hasAccess() )
			{
				$append_actions[] = array();
				$append_actions[] = array ( 
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
	
	function getNewCardActions( $project_it )
	{
		$append_actions = array();
		$filter_values = $this->getFilterValues();
		
		$object = $this->getObject();
		$object->setVpdContext($project_it);
		
		$method = new ObjectCreateNewWebMethod($object);
		$method->setRedirectUrl('donothing');
		
		$parms = array (
				'area' => $this->getPage()->getArea()
		);

		if ( in_array($filter_values['type'], array('','hide','all')) || strpos($filter_values['type'],'none') !== false )
		{
			$uid = 'append-issue';
			$append_actions[$uid] = array (
				'name' => $this->object->getDisplayName(),
				'uid' => $uid,
				'url' => $method->getJSCall($parms)
			);
		}

		$type_it = getFactory()->getObject('pm_IssueType')->getRegistry()->Query(
				array (
						new FilterVpdPredicate($project_it->get('VPD')),
						new FilterAttributePredicate('ReferenceName', $filter_values['type']),
						new SortOrderedClause()
				)
		);
		while ( !$type_it->end() )
		{
			$parms['Type'] = $type_it->getId();
			$uid = 'append-issue-'.$type_it->get('ReferenceName');
			
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
					'url' => $url != '' ? preg_replace('/\%query\%/', 'template='.$template_it->getId(), $url) : $method->getJSCall($parms)
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

		if ( getSession()->getProjectIt()->getMethodologyIt()->get('IsRequestOrderUsed') == 'Y' )
		{
			array_push( $cols, 'OrderNum');
		}
		
		unset($cols[array_search('Watchers', $cols)]);
		unset($cols[array_search('Attachment', $cols)]);
		unset($cols[array_search('Links', $cols)]);
		unset($cols[array_search('Spent', $cols)]);
		
		return $cols;
	}
	
    function getSortAttributeClause( $field )
	{
	    $parts = preg_split('/\./', $field);
	    
	    if ( $parts[0] == 'Owner' )
	    {
	        return new IssueOwnerSortClause();
	    }
	    
		return parent::getSortAttributeClause( $field );
	}
	
 	function getFilterActions()
	{
	    $actions = parent::getFilterActions();
	    
	    if ( in_array($this->getReportBase(), array('releaseburndown', 'releaseburnup', 'projectburnup')) )
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
	    if ( in_array($this->getReportBase(), array('releaseburndown', 'releaseburnup', 'projectburnup')) )
	    {
	        return $this->getFiltersReleaseBurndown();
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
			$this->buildFilterWasTransition(),
			new FilterStateTransitionMethod( getFactory()->getObject('IssueState') ),
			$this->buildFilterPriority(),
			new ViewSubmmitedAfterDateWebMethod(),
			new ViewSubmmitedBeforeDateWebMethod(),
			new ViewModifiedBeforeDateWebMethod(),
			new ViewModifiedAfterDateWebMethod(),
			$this->buildFilterOwner(),
			new ViewRequestTagWebMethod(),
			$this->buildFilterAuthor(),
			new ViewRequestTaskTypeWebMethod(),
			new ViewRequestTaskStateWebMethod(),
            new ViewRequestVersionWebMethod()
		);

		if ( getFactory()->getObject('RequestType')->getAll()->count() > 0 ) {
			$filters[] = $this->buildFilterType();
		}
			
		if ( $methodology_it->HasFeatures() ) {
			$filters[] = $this->buildFilterFunction();
		}
		
		if ( $methodology_it->HasReleases() )
		{
			$release = getFactory()->getObject('Release');
			$release->addFilter( new ReleaseTimelinePredicate('current') );
			$releases = new FilterObjectMethod( $release, translate('Релизы'), 'release');
			$filters = array_merge( array_slice($filters, 0, 1), array( $releases ), array_slice($filters, 1) );
		}
		
		if ( $methodology_it->HasPlanning() )
		{
		    $iteration = getFactory()->getObject('Iteration');
		    $iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
			$filters[] = new FilterObjectMethod( $iteration, translate('Итерации'), 'iteration');
		}
		
		array_push( $filters, $this->buildFilterSubmittedVersion() );

		$strategy = $methodology_it->getEstimationStrategy();
		$filter = $strategy->getEstimationFilter();
		if ( is_object($filter) ) array_push( $filters, $filter );
		
	    $filter = $this->buildSnapshotFilter();
	    if ( is_object($filter) ) $filters[] = $filter;
	    
		return array_merge( $filters, parent::getFilters() );
	}
	
 	function getFilterPredicates()
	{
 		global $_REQUEST, $model_factory;

 		$values = $this->getFilterValues();
 		
 		$predicates = array();
 		
		$predicates[] = new StatePredicate( $values['state'] );
		$predicates[] = new FilterAttributePredicate( 'Priority', $values['priority']);
		$predicates[] = new IssueOwnerUserPredicate($values['owner']);
		$predicates[] = new FilterAttributePredicate('Type', $values['type']);
		$predicates[] = new FilterSubmittedAfterPredicate($values['submittedon']);
		$predicates[] = new FilterSubmittedBeforePredicate($values['submittedbefore']);
		$predicates[] = new RequestSubmittedFilter($values['subversion']);
		$predicates[] = new RequestFeatureFilter($values['function']);
		$predicates[] = new RequestTagFilter($values['tag']);
		$predicates[] = new RequestReleasePredicate($values['release']);
		$predicates[] = new RequestReleasePredicate($_REQUEST['plannedrelease']);
		$predicates[] = new RequestTestResultPredicate($_REQUEST['test']);
		$predicates[] = new RequestAuthorFilter( $values['author'] );
		$predicates[] = new TransitionObjectPredicate( $this->getObject(), $values['transition'] );
		$predicates[] = new TransitionWasPredicate( $values['was-transition'] );
		$predicates[] = new RequestTaskTypePredicate( $values['tasktype'] );
		$predicates[] = new RequestTaskStatePredicate( $values['taskstate'] );
		$predicates[] = new RequestVersionFilter($values['version']);
		$predicates[] = new RequestIterationFilter($values['iteration']);
		$predicates[] = new RequestIterationFilter($_REQUEST['iterations']);

		$trace = $model_factory->getObject('pm_ChangeRequestTrace');

		array_push($predicates, new RequestTracePredicate( $_REQUEST['trace'] ) );

		$predicates[] = new FilterModifiedAfterPredicate($values['modifiedafter']);
		$predicates[] = new FilterModifiedBeforePredicate($values['modifiedbefore']);
		
		$strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
		
		$predicate = $strategy->getEstimationPredicate( $values['estimation'] );
		
		if ( is_object($predicate) ) array_push( $predicates, $predicate );
		
		return array_merge($predicates, parent::getFilterPredicates());
	}	
	
	protected function buildFilterState()
	{
		if ( $this->getListRef() instanceof RequestBoard )
		{
			return new StateExFilterWebMethod($this->getListRef()->getBoardAttributeIterator());
		}
		else
		{
			if ( getSession()->getProjectIt()->IsPortfolio() )
			{
				$metastate = getFactory()->getObject('StateMeta');
		 		$metastate->setAggregatedStateObject(getFactory()->getObject('IssueState'));
		 		$state_it = $metastate->getRegistry()->getAll();
			} 
			else {
				$state_it = getFactory()->getObject('IssueState')->getAll();
			}
			return new StateExFilterWebMethod($state_it);
		}
	}
	
	protected function buildFilterType()
	{
		$type_method = new FilterObjectMethod( getFactory()->getObject('RequestType'), translate('Тип'), 'type');
		$type_method->setIdFieldName( 'ReferenceName' );
		$type_method->setNoneTitle( getFactory()->getObject('Request')->getDisplayName() );
		return $type_method;
	}
	
	protected function buildFilterWasTransition()
	{
		$filter = new FilterStateTransitionMethod( getFactory()->getObject('IssueState') );
		$filter->setValueParm('was-transition');
		$filter->setCaption(text(1887));
		return $filter;
	}
	
	protected function buildFilterOwner()
	{
		return new FilterObjectMethod( getFactory()->getObject('ProjectUser'), translate($this->getObject()->getAttributeUserName('Owner')), 'owner' );
	}
	
	protected function buildFilterAuthor()
	{
		$author = getFactory()->getObject('IssueActualAuthor');
		$count = $author->getRecordCount();
		if ( $count < 21 )
		{
			$filter = new FilterObjectMethod($author, translate('Автор'), 'author');
			$filter->setHasNone(false);
		}
		else
		{
			$filter = new FilterAutoCompleteWebMethod($author, translate('Автор'), 'author');
		}
		$filter->setIdFieldName('Login');
		return $filter;
	}

	protected function buildFilterFunction()
	{
		$count = getFactory()->getObject('Feature')->getRecordCount();
		
		if ( $count < 50 )
		{
			$filter = new FilterObjectMethod(getFactory()->getObject('Feature'), '', 'function');
		}
		else
		{
			$filter = new FilterAutoCompleteWebMethod(getFactory()->getObject('Feature'), '', 'function');
		}
		
		return $filter;
	}
	
	protected function buildFilterPriority()
	{
		$priority = getFactory()->getObject('Priority');
		$filter = new FilterObjectMethod($priority);

		if ( $this->getReportBase() == 'issuesboardcrossproject' ) {
			$registry = $priority->getRegistry();
			$registry->setLimit(3);
			$values = $registry->getAll()->idsToArray();
			$filter->setDefaultValue(join(',',$values));
		}
		
		return $filter;
	}

	protected function buildFilterSubmittedVersion()
	{
		$filter = new FilterAutoCompleteWebMethod(getFactory()->getObject('Version'), translate('Обнаружено в версии'), 'subversion');
		return $filter;
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
					$estimation = $release_it->getTotalWorkload();
					list( $capacity, $maximum, $actual_velocity ) = $release_it->getEstimatedBurndownMetrics();
					echo sprintf(
						getSession()->getProjectIt()->IsPortfolio() ? text(2076) : text(2053),
						$release_it->getDateFormatShort('StartDate'),
						$release_it->get('FinishDate') == '' ? '?' : $release_it->getDateFormatShort('FinishDate'),
						$this->estimation_strategy->getDimensionText(round($maximum, 1)),
						$estimation > $maximum ? 'label label-important' : ($maximum > 0 && $estimation < $maximum ? 'label label-success': ''),
						$this->estimation_strategy->getDimensionText(round($estimation, 1))
					);
				}
				break;

			case 'Iterations':
				echo ' &nbsp; &nbsp; &nbsp; &nbsp; ';

				$release_it = $this->getListRef()->getGroupIt();
				$release_it->moveToId($object_it->get($group_field));

				if ( $release_it->getId() > 0 ) {
					$estimation = $release_it->getEstimation();
					list( $capacity, $maximum, $actual_velocity ) = $release_it->getEstimatedBurndownMetrics();
					echo sprintf(
							getSession()->getProjectIt()->IsPortfolio() ? text(2076) : text(2053),
							$release_it->getDateFormatShort('StartDate'),
							$release_it->get('FinishDate') == '' ? '?' : $release_it->getDateFormatShort('FinishDate'),
							$this->estimation_strategy->getDimensionText(round($maximum, 1)),
							$estimation > $maximum ? 'label label-important' : ($maximum > 0 && $estimation < $maximum ? 'label label-success': ''),
							$this->estimation_strategy->getDimensionText(round($estimation, 1))
					);
				}
				break;

			case 'Project':
				$project_it = $this->getListRef()->getGroupIt();
				$project_it->moveToId($object_it->get($group_field));
				echo $this->getView()->render('pm/RowGroupActions.php', array (
					'actions' => $this->getNewCardActions($project_it)
				));
				break;
		}
	}

	function getAssigneeWorkload() {
		return $this->workload;
	}

	protected function buildAssigneeWorkload( $iterator )
	{
		$object = getFactory()->getObject(get_class($this->getObject()));
		$object->resetPersisters();
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
}