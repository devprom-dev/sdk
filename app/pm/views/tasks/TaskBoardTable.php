<?php

include "TaskList.php";
include "TaskTraceList.php";
include "TaskChart.php";
include "TaskBoardList.php";
include "IteratorExportTaskBoard.php";
include SERVER_ROOT_PATH.'pm/methods/c_task_methods.php';
include SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedBeforeDateWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/ViewSubmmitedAfterDateWebMethod.php";

class TaskBoardTable extends PMPageTable
{
	var $workload = array();
	
	function getViewFilter()
	{
		return new ViewTaskListWebMethod();
	}
	
	function buildRelatedDataCache()
	{
		$list = $this->getListRef();
		
		if ( $list->getGroup() == 'Assignee' )
		{
			$this->buildAssigneeWorkload();
		}
	}
	
	function getPredicates($filters)
	{
		$predicates = parent::getPredicates($filters);

		if ( $this->getTable()->getReportBase() != 'issuesboardcrossproject' )
		{
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
				return new TaskChart( $this->getObject() );
				
			default:
				return new TaskBoardList( $this->getObject(), $this->is_finished );
		}
	}
	
	function hasCrossProjectFilter()
	{
		if ( $this->getReportBase() == 'tasksboardcrossproject' ) return true;
		
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
	    global $model_factory;
	    
		$iteration = $model_factory->getObject('Iteration');
		
 		$iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
		
		$iterations = new FilterObjectMethod($iteration, translate('Итерация'), 'release', false);
		
		$iterations->setType('singlevalue');
	    
		return array( $iterations );
	}
	
 	function getFiltersBase()
	{
		$user = getFactory()->getObject('cms_User');

		$user->addFilter( new FilterInPredicate(getFactory()->getObject('pm_Participant')->getAll()->fieldToArray('SystemUser')));
		
		$assignee_filter = new FilterObjectMethod( $user, text(753), 'taskassignee' );
		
		$type_method = new FilterObjectMethod( getFactory()->getObject('pm_TaskType'), translate('Тип'), 'tasktype');
		
		$type_method->setIdFieldName( 'ReferenceName' );
		
		$filters = array(
			new ViewTaskStateWebMethod(),
            $this->buildIterationFilter(),
			$type_method,
			new FilterObjectMethod( getFactory()->getObject('Priority'), '', 'taskpriority' ),
			$assignee_filter,
			$this->buildFilterWasTransition(),
			new ViewSubmmitedAfterDateWebMethod(),
			new ViewSubmmitedBeforeDateWebMethod(),
			new ViewModifiedBeforeDateWebMethod(),
			new ViewModifiedAfterDateWebMethod()
		);

		return array_merge( $filters, PMPageTable::getFilters() ); 		
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
	
	protected function buildFilterWasTransition()
	{
		$filter = new FilterStateTransitionMethod( getFactory()->getObject('TaskState') );
		$filter->setValueParm('was-transition');
		$filter->setCaption(text(1887));
		return $filter;
	}
	
 	function getFilterPredicates()
	{
		global $_REQUEST;
		
		$values = $this->getFilterValues();
		
		$predicates = array(
			new StatePredicate( $values['taskstate'] ),
			new FilterAttributePredicate( 'Priority', $values['taskpriority'] ),
			new FilterAttributePredicate( 'TaskType', $values['tasktype'] ),
			new FilterAttributePredicate( 'Assignee', $values['taskassignee'] ),
			new FilterAttributePredicate( 'Release', $values['iteration'] ),
			new TaskReleasePredicate($values['issue-release']),
 			new TaskVersionPredicate( $values['stage'] ),
			new FilterSubmittedAfterPredicate( $values['submittedon'] ),
			new FilterSubmittedBeforePredicate( $values['submittedbefore'] ),
			new FilterAttributePredicate( 'ChangeRequest', $_REQUEST['issue'] )
		);		

		$predicates[] = new FilterModifiedAfterPredicate($values['modifiedafter']);
		$predicates[] = new FilterModifiedBeforePredicate($values['modifiedbefore']);
 		$predicates[] = new TaskBindedToObjectPredicate($_REQUEST['trace']);
		$predicates[] = new TransitionWasPredicate( $values['was-transition'] );
 		
		return array_merge(parent::getFilterPredicates(), $predicates);
	}
	
	function getActions()
	{
		$actions = array();

		$method = new ExcelExportWebMethod();
		$url = $method->getJSCall( translate('Задачи') );

		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $url ) );

		$method = new BoardExportWebMethod();
		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->getJSCall( 'IteratorExportTaskBoard' ) ) );

		$method = new HtmlExportWebMethod();
		array_push($actions, array( 'name' => $method->getCaption(),
			'url' => $method->getJSCall() ) );

		///
		$list = $this->getListRef();

		if ( $list->IsNeedToSelect() )
		{
            if ( $actions[array_pop(array_keys($actions))]['name'] != '' ) $actions[] = array();
			$actions[] = array( 
			    'name' => translate('Выбрать все'),
				'url' => 'javascript: checkRowsTrue(\''.$list->getId().'\');', 
				'title' => text(969),
				'radio' => true
			);
		}
		
		$bulk_actions_access = 
				getFactory()->getAccessPolicy()->can_modify($this->getObject()) 
				&& !getSession()->getProjectIt()->object instanceof Portfolio;
	    
		if ( $bulk_actions_access )
		{
			$actions[] = array( 
					'name' => translate('Массовые операции'),
					'url' => 'javascript: processBulkMethod();', 
					'title' => text(651)
			);
		}
		
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
		
		$uid = 'append-task';
		
		if ( $method->hasAccess() )
		{
			$parms = array (
					'area' => $this->getPage()->getArea()
			);
				
		    if ( $this->getReportBase() == 'mytasks' )
		    {
		    	$parms['Assignee'] = getSession()->getParticipantIt()->getId();
		    }
			    
			$append_actions[$uid] = array ( 
    			'name' => $method->getObject()->getDisplayName(),
				'uid' => $uid,
				'url' => $method->getJSCall($parms)
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

	protected function buildAssigneeWorkload()
	{
		$list = $this->getListRef();
		$task_ids = $list->getIteratorRef()->idsToArray();
		
		$object = getFactory()->getObject('Task');
		$object->addFilter( new FilterInPredicate($task_ids) );
		
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
		
		$project_it = getSession()->getProjectIt();
		$iteration_registry = getFactory()->getObject('Iteration')->getRegistry();
		$part_registry = getFactory()->getObject('Participant')->getRegistry();
		
		foreach( $this->workload as $user_id => $data )
		{
			$iteration_it = $iteration_registry->Query(
					array (
							new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED),
							new IterationUserHasTasksPredicate($user_id),
							new FilterVpdPredicate(),
							new EntityProjectPersister(),
							new SortAttributeClause('Project')
					)
			);

			$data = array();
			$this->workload[$user_id]['Iterations'] = array(); 
			
			if ( $user_id == '' ) continue;
			
			while( !$iteration_it->end() )
			{
				$self_it = $iteration_it->getRef('Project');
				$part_it = $part_registry->Query(
						array (
								new FilterAttributePredicate('SystemUser', $user_id),
								new FilterAttributePredicate('Project', $self_it->getId()),
						)
				);
				
				if( $part_it->getId() < 1 )
				{
					$iteration_it->moveNext();
					continue;
				}
				
				$data['leftwork'] = $iteration_it->getLeftWorkParticipant( $part_it );
				if ( $data['leftwork'] < 1 )
				{
					$iteration_it->moveNext();
					continue;
				}

				$project_prefix = ($self_it->getId() != $project_it->getId() ? '{'.$self_it->get('CodeName').'} ' : '');
				
				$data['title'] = $project_prefix.translate('Итерация').': '.$iteration_it->getDisplayName();
				$data['number'] = $iteration_it->get('ShortCaption');
				$data['capacity'] = $iteration_it->getLeftCapacity() * $part_it->get('Capacity');
				
				$method = new ObjectModifyWebMethod($iteration_it);
				if ( $method->hasAccess() )
				{
					$method->setRedirectUrl('donothing');
					$data['url'] = $method->getJSCall(); 
				}

				$this->workload[$user_id]['Iterations'][] = $data;
				$iteration_it->moveNext();
			}
		}
	}
}