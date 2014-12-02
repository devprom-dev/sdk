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
		
		if ( $list->getGroup() == 'AssigneeUser' )
		{
			$object = getFactory()->getObject('Task');
			
			$object->addFilter( new FilterInPredicate($list->getIteratorRef()->idsToArray()) );
			
			// cache aggregates on workload and spent time
			$planned_aggregate = new AggregateBase( 'AssigneeUser', 'Planned', 'SUM' );
			
			$object->addAggregate( $planned_aggregate );

			$left_aggregate = new AggregateBase( 'AssigneeUser', 'LeftWork', 'SUM' );
			
			$object->addAggregate( $left_aggregate );

			$fact_aggregate = new AggregateBase( 'AssigneeUser', 'Fact', 'SUM' );
			
			$object->addAggregate( $fact_aggregate );
			
			$task_it = $object->getAggregated();
			
			while( !$task_it->end() )
			{
				$this->workload[$task_it->get('AssigneeUser')]['Planned'] = $task_it->get($planned_aggregate->getAggregateAlias());
				
				if ( $this->workload[$task_it->get('AssigneeUser')]['Planned'] == '' ) $this->workload[$task_it->get('AssigneeUser')]['Planned'] = 0;

				$this->workload[$task_it->get('AssigneeUser')]['LeftWork'] = $task_it->get($left_aggregate->getAggregateAlias());

				if ( $this->workload[$task_it->get('AssigneeUser')]['LeftWork'] == '' ) $this->workload[$task_it->get('AssigneeUser')]['LeftWork'] = 0;
				
				$this->workload[$task_it->get('AssigneeUser')]['Fact'] = $task_it->get($fact_aggregate->getAggregateAlias());

				if ( $this->workload[$task_it->get('AssigneeUser')]['Fact'] == '' ) $this->workload[$task_it->get('AssigneeUser')]['Fact'] = 0;
				
				$task_it->moveNext();
			}
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
			new ViewSubmmitedAfterDateWebMethod(),
			new ViewSubmmitedBeforeDateWebMethod(),
			new ViewModifiedBeforeDateWebMethod(),
			new ViewModifiedAfterDateWebMethod(),
		);

		return array_merge( $filters, PMPageTable::getFilters() ); 		
	}

	protected function buildIterationFilter()
	{
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() )
		{
			$release = getFactory()->getObject('Release');
	 		$release->addFilter( new ReleaseTimelinePredicate('not-passed') );
			return new FilterObjectMethod($release, translate('Релиз'), 'release');
		}
		else
		{ 
			$iteration = getFactory()->getObject('Iteration');
	 		$iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
			return new FilterObjectMethod($iteration, translate('Итерация'), 'iteration');
		}
	}
	
 	function getFilterPredicates()
	{
		global $_REQUEST;
		
		$values = $this->getFilterValues();
		
		$predicates = array(
			new StatePredicate( $values['taskstate'] ),
			new FilterAttributePredicate( 'Priority', $values['taskpriority'] ),
			new FilterAttributePredicate( 'TaskType', $values['tasktype'] ),
			new TaskAssigneeUserPredicate( $values['taskassignee'] ),
			new FilterAttributePredicate( 'Release', $values['iteration'] ),
			new TaskReleasePredicate($values['release']),
 			new TaskVersionPredicate( $values['stage'] ),
			new FilterSubmittedAfterPredicate( $values['submittedon'] ),
			new FilterSubmittedBeforePredicate( $values['submittedbefore'] ),
			new FilterAttributePredicate( 'ChangeRequest', $_REQUEST['issue'] )
			);		

		$predicates[] = new FilterModifiedAfterPredicate($values['modifiedafter']);
		$predicates[] = new FilterModifiedBeforePredicate($values['modifiedbefore']);
 		$predicates[] = new TaskBindedToObjectPredicate($_REQUEST['trace']);
		
		return array_merge(parent::getFilterPredicates(), $predicates);
	}
	
	function getActions()
	{
		$actions = array();

		$list = $this->getListRef();
		
		$values = $this->getFilterValues();
		
		$object = $this->getObject();
		
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
		if ( getFactory()->getAccessPolicy()->can_modify($object) )
		{
			if ( $actions[count($actions) - 1]['name'] != '' )
			{
				array_push($actions, array() );
			}

			array_push($actions, array( 'name' => translate('Выбрать все'),
				'url' => 'javascript: checkRowsTrue(\''.$list->getId().'\');', 'title' => text(969) ) );

			array_push($actions, array( 'name' => translate('Массовые операции'),
				'url' => 'javascript: processBulkMethod();', 'title' => text(651) ) );
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
}