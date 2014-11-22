<?php

include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedBeforeDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedAfterDateWebMethod.php";
include_once SERVER_ROOT_PATH."pm/methods/c_date_methods.php";
include_once SERVER_ROOT_PATH."pm/methods/MakeSnapshotWebMethod.php";

include "RequestList.php";
include "RequestChart.php";
include "RequestBoard.php";
include "RequestTraceList.php";

class RequestTable extends PMPageTable
{
 	var $view_filter;
 	
 	function __construct( & $object )
 	{
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
		
		$method = new BoardExportWebMethod();
		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->getJSCall( 'IteratorExportIssueBoard' ) ) );

		$method = new HtmlExportWebMethod();
		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->getJSCall() ) );

		$module_it = $model_factory->getObject('Module')->getExact('issues-import');
	    
	    if ( getFactory()->getAccessPolicy()->can_read($module_it) )
	    {
        	if ( $actions[count($actions) - 1]['name'] != '' ) $actions[] = array();
        	
	        $item = $module_it->buildMenuItem('?view=import&mode=xml&object=request');
	        
		    $actions[] = array( 
                'name' => translate('Импортировать'),
				'url' => $item['url']
            );
	    }
	    
		///
		if ( getFactory()->getAccessPolicy()->can_modify($this->object) )
		{
			$list = $this->getListRef();
			
			if ( $list->IsNeedToSelect() )
			{
				$actions[] = array();

				$actions[] = array( 
				    'name' => translate('Выбрать все'),
					'url' => 'javascript: checkRowsTrue(\''.$list->getId().'\');', 
					'title' => text(969),
					'radio' => true
				);

				$actions[] = array( 
                    'name' => translate('Массовые операции'),
					'url' => 'javascript: processBulkMethod();', 
                    'title' => text(651)
                );
			}
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

	function getDeleteActions()
	{
		if( !$this->IsNeedToDelete() ) return array(); 
		
		$method = new BulkDeleteWebMethod();
		
		$actions['delete'] =  array ( 
				'name' => $method->getCaption(),
				'url' => $method->getBulkJSCall( $this->getObject() ),
				'title' => $method->getDescription()
		);
		
		return $actions;
	}
	
	function getNewActions()
	{
	    global $model_factory;
	    
	    $append_actions = array();
	    
		$filter = $this->getViewFilter();
		
		$filter->setFilter( $this->getFiltersName() );
		
		$method = new ObjectCreateNewWebMethod($this->getObject());
		
		$method->setRedirectUrl('donothing');
		
		if ( $method->hasAccess() )
		{
			$parms = array (
					'area' => $this->getPage()->getArea()
			);
				
			$report = $this->getReportBase();
			    
		    if ( $report == 'myissues' )
		    { 
		    	$parms['Owner'] = getSession()->getParticipantIt()->getId(); 
			}
				
			$type_it = getFactory()->getObject('pm_IssueType')->getRegistry()->Query(
					array (
							new FilterBaseVpdPredicate()
					)
			);
			
			while ( !$type_it->end() )
			{
				$parms['Type'] = $type_it->getId();
				
				$uid = 'append-issue-'.$type_it->get('ReferenceName');
				
				$append_actions[$uid] = array ( 
					'name' => $type_it->getDisplayName(),
					'uid' => $uid,
					'url' => $url != '' 
							? preg_replace('/\%query\%/', 'Type='.$type_it->getId(), $url) 
							: $method->getJSCall($parms, $type_it->getDisplayName())
				);
				
				$type_it->moveNext();
			}
			
			unset($parms['Type']);
			
			$uid = 'append-issue';
			
			$append_actions[$uid] = array ( 
				'name' => $this->object->getDisplayName(),
				'uid' => $uid,
				'url' => $url != '' ? preg_replace('/\%query\%/', '', $url) : $method->getJSCall($parms)
			);
			
			$template_it = getFactory()->getObject('RequestTemplate')->getAll();
			
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
		}

		$method = new ObjectCreateNewWebMethod(getFactory()->getObject('Feature'));
		
		$method->setRedirectUrl('donothing');
		
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasFeatures() && $method->hasAccess() )
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
		
		return $append_actions;
	}
	
    function getVersioningActions()
    {
    	$actions = parent::getVersioningActions();
    	
    	if ( getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('cms_Snapshot')) )
		{
			$method = new MakeSnapshotWebMethod();
			
			$actions[] = array( 
				'name' => $method->getCaption(),
				'url' => $method->getJSCall(),
				'uid' => 'save-version'
			);
		}

		return $actions;
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
	    
	    if ( in_array($this->getReportBase(), array('releaseburndown', 'releaseburnup')) )
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
	    if ( in_array($this->getReportBase(), array('releaseburndown', 'releaseburnup')) )
	    {
	        return $this->getFiltersReleaseBurndown();
	    }
	    
	    return $this->getFiltersBase();
	}
	
	function getFiltersReleaseBurndown()
	{
	    global $model_factory;
	    
		$release = $model_factory->getObject('Release');
		
		$release->addFilter( new ReleaseTimelinePredicate('current') );
		
		$releases = new FilterObjectMethod( $release, translate('Релизы'), 'release', false);
		
		$releases->setType( 'singlevalue' );
		
		return array( $releases );
	}
	
	function getFiltersBase()
	{
		global $model_factory;
		
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		$type_method = new FilterObjectMethod( $model_factory->getObject('pm_IssueType'), translate('Тип'), 'type');
		
		$type_method->setIdFieldName( 'ReferenceName' );
		
		$type_method->setNoneTitle( $model_factory->getObject('Request')->getDisplayName() );
		
		// build Responsible filter
		$worker = $model_factory->getObject('pm_Participant');

		$user = $model_factory->getObject('cms_User');
		
		$user->addFilter( new FilterInPredicate($worker->getAll()->fieldToArray('SystemUser')) );
		
		$owner_filter = new FilterObjectMethod( $user, translate($this->object->getAttributeUserName('Owner')), 'owner' );
		
		$filters = array (
			$this->buildFilterState(),
			new FilterStateTransitionMethod( $model_factory->getObject('IssueState') ),
			$type_method, 
			new FilterObjectMethod( $model_factory->getObject('Priority'), '', 'priority'),
			new ViewSubmmitedAfterDateWebMethod(),
			new ViewSubmmitedBeforeDateWebMethod(),
			new ViewModifiedBeforeDateWebMethod(),
			new ViewModifiedAfterDateWebMethod(),
			$owner_filter,
			new ViewRequestTagWebMethod(),
			$this->buildFilterAuthor(),
			new ViewRequestTaskTypeWebMethod(),
			new ViewRequestTaskStateWebMethod(),
            new ViewRequestVersionWebMethod()
		);

		if ( $methodology_it->HasFeatures() )
		{
			$filters[] = $this->buildFilterFunction();
		}
		
		if ( $methodology_it->HasReleases() )
		{
			$release = $model_factory->getObject('Release');
			$release->addFilter( new ReleaseTimelinePredicate('current') );
			
			$releases = new FilterObjectMethod( $release, translate('Релизы'), 'release');
			
			$filters = array_merge( array_slice($filters, 0, 1), array( $releases ), array_slice($filters, 1) );
		}
		
		if ( $methodology_it->HasPlanning() )
		{
		    $iteration = $model_factory->getObject('Iteration');
		    
		    $iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
		    
			$filters[] = new FilterObjectMethod( $iteration, translate('Итерации'), 'iteration');
		}
		
		array_push( $filters, new ViewRequestSubmittedWebMethod() );

		$strategy = $methodology_it->getEstimationStrategy();
		
		$filter = $strategy->getEstimationFilter();
		
		if ( is_object($filter) ) array_push( $filters, $filter );
		
	    $filter = $this->buildSnapshotFilter();
	    
	    if ( is_object($filter) ) $filters[] = $filter; 
	    
	    $list_object = $this->getListRef();
	    
	    $report_name = $this->getReport();

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
		$predicates[] = new FilterAttributePredicate('Function', $values['function']);
		$predicates[] = new RequestTagFilter($values['tag']);
		$predicates[] = new RequestReleasePredicate($values['release']);
		$predicates[] = new RequestTestResultPredicate($_REQUEST['test']);
		$predicates[] = new RequestAuthorFilter( $values['author'] );
		$predicates[] = new TransitionObjectPredicate( $this->getObject(), $values['transition'] );
		$predicates[] = new RequestTaskTypePredicate( $values['tasktype'] );
		$predicates[] = new RequestTaskStatePredicate( $values['taskstate'] );
		$predicates[] = new RequestVersionFilter($values['version']);
		$predicates[] = new RequestIterationFilter($values['iteration']);
		
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
		return new ViewRequestStateWebMethod();
	}
	
	protected function buildFilterAuthor()
	{
		$count = getFactory()->getObject('IssueAuthor')->getRecordCount();
		
		if ( $count < 50 )
		{
			$filter = new FilterObjectMethod(getFactory()->getObject('IssueAuthor'), translate('Автор'), 'author');
			
			$filter->setHasNone(false);
		}
		else
		{
			$filter = new FilterAutoCompleteWebMethod(getFactory()->getObject('IssueAuthor'), translate('Автор'), 'author');
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
} 