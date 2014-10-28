<?php
 
define(RESULT_FAILED, 'Провален');
define(RESULT_SUCCEEDED, 'Успешно пройден');
define(RESULT_FIXED, 'Исправлена');
define(RESULT_RESOLVED, 'Выполнена');
define(RESULT_FIXEDINDIRECTLY, 'Уже сделана');
define(RESULT_CANTREPRODUCE, 'Не воспроизводится');
define(RESULT_FUNCTIONSASDESIGNED, 'Работает как задумано');
define(RESULT_SCENARIOPREPARED, 'Подготовлен тестовый набор');

include "TaskIterator.php";

include "predicates/TaskAssigneeUserPredicate.php";
include "predicates/TaskCategoryPredicate.php";
include "predicates/TaskTypeBasePredicate.php";
include "predicates/TaskVersionPredicate.php";
include "predicates/TaskFromDatePredicate.php";
include "predicates/TaskUntilDatePredicate.php";
include "predicates/TaskBindedToObjectPredicate.php";
include "sorts/TaskAssigneeSortClause.php";

include_once SERVER_ROOT_PATH."pm/classes/common/persisters/WatchersPersister.php";

class Task extends MetaobjectStatable 
{
 	var $type_cache;
 	
 	function __construct( $registry = null ) 
 	{
		parent::__construct('pm_Task', $registry);
 	}
	
	function getPage() 
	{
		return getSession()->getApplicationUrl($this).'tasks/board?';
	}

	function createIterator() 
	{
		return new TaskIterator($this);
	}

	function getAttachmentUrl( $task_it ) 
	{
	}
	
 	function getOrderStep()
	{
	    $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
	    
	    return is_object($methodology_it) && $methodology_it->get('IsRequestOrderUsed') == 'Y'
	        ? 1 : parent::getOrderStep();
	}
	
	function getDefaultAttributeValue( $name )
	{
		global $_REQUEST, $model_factory;

		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		if( $name == 'Assignee' && !$methodology_it->IsParticipantsTakesTasks() && $_REQUEST['TaskType'] > 0 ) 
		{
			$TaskType = $_REQUEST['TaskType']; 
			$Assignee = $_REQUEST['Assignee'];

			$tasktype = $model_factory->getObject('pm_TaskType');
			$tasktype_it = $tasktype->getExact($TaskType);

			// автоматически подставим единственного исполнителя
			//
			switch($tasktype_it->get('ReferenceName')) 
			{
				case 'development':
				case 'support':
					$role_kind = 'developer'; // developer;
					break;
					
				case 'testing':
					$role_kind = 'tester'; // tester;
					break;
					
				case 'analysis':
					$role_kind = 'analyst'; // analyst;
					break;
					
				case 'accepting':
					$role_kind = 'client'; // client;
					break;
					
				case 'design':
					$role_kind = ''; // designer;
					break;
					
				default:
					$role_kind = '';
			}

			if($role_kind != '') 
			{
				$role = getFactory()->getObject('pm_ParticipantRole');
				
				$project_role = getFactory()->getObject('pm_ProjectRole');
				$project_role_it = $project_role->getByRef('ReferenceName', $role_kind);
				
				$role_it = $role->getInArray('ProjectRole', $project_role_it->idsToArray());
				if($role_it->count() == 1) {
					$Assignee = $role_it->get('Participant'); 
					return $Assignee;
				}
			}
		}
		elseif( $name == 'Release' )
		{
			return $_REQUEST['Release'];
		}
		elseif( $name == 'TaskType' ) 
		{
		    return $model_factory->getObject('TaskType')->getByRef('ReferenceName', 'development')->getId();
		}

		return parent::getDefaultAttributeValue( $name );
	}
	
	function IsDeletedCascade( $object )
	{
	    if ( is_a($object, 'Request') ) return true;
	    
		return false;
	}

	function _cacheTypes()
	{
		global $model_factory;
		
		if ( !is_object($this->type_cache) )
		{
			$types = $model_factory->getObject('TaskType');
			$this->type_cache = $types->getAll(); 
		}
		
		$this->type_cache->moveFirst();
	}
	
	function getTypesMap()
	{
		$this->_cacheTypes();
		$map = array();
		
		while ( !$this->type_cache->end() )
		{
			$map[$this->type_cache->get('ReferenceName')] = $this->type_cache->getId();
			$this->type_cache->moveNext();
		}
		
		return $map;
	}

	function add_parms( $parms )
	{
		global $model_factory;
		
		if ( $parms['LeftWork'] == '' )
		{
			$parms['LeftWork'] = $parms['Planned'];
		}
		
		if ( $parms['Release'] == '' ) $parms['Release'] = $this->getDefaultAttributeValue('Release');
		
		if ( $parms['ChangeRequest'] > 0 )
		{
			$issue = $model_factory->getObject('pm_ChangeRequest');

			$issue_it = $issue->getExact($parms['ChangeRequest']);
			
			if ( $parms['Priority'] == '' ) $parms['Priority'] = $issue_it->get('Priority');
			 
			if ( $parms['OrderNum'] == '' ) $parms['OrderNum'] = $issue_it->get('OrderNum'); 
		}

		return parent::add_parms( $parms );
	}
	
	function modify_parms( $object_id, $parms )
	{
		global $model_factory;

		$object_it = $this->getExact($object_id);

		if ( array_key_exists('Release', $parms) && $parms['Release'] == '' )
		{
			$parms['Release'] = $this->getDefaultAttributeValue('Release');
		}
		
		if ( $parms['Planned'] != '' && $object_it->get('Planned') != $parms['Planned'] )
		{
			$parms['LeftWork'] = $parms['Planned'];
		}
		
		if ( $parms['Transition'] > 0 )
		{
			$target_state = getFactory()->getObject('Transition')->
					getExact($parms['Transition'])->getRef('TargetState')->get('ReferenceName');
			
			switch ( $target_state )
			{
				default:
					if ( in_array($target_state, $this->getTerminalStates()) )
					{
						// if the task is marked as completed then
						// reset left work value to 0
						//
						$parms['LeftWork'] = 0;
						
						$parms['TransitionComment'] = translate('Результат').': '.
							( $parms['Result'] == '' ? $object_it->get('Result') : $parms['Result'] ).
							chr(10).$parms['TransitionComment'];
					}				
					break;
			}
		}

		return parent::modify_parms($object_id, $parms);
	}
}