<?php

namespace Devprom\ProjectBundle\Service\Task;

class TaskDefaultsService
{
	static function getAssignee( $type_id )
	{
		// use rules defined on the project level
		$tasktype_it = getFactory()->getObject('pm_TaskType')->getExact($type_id);
		if ( $tasktype_it->get('ProjectRole') != '' )
		{
			return getFactory()->getObject('Participant')->getRegistry()->Query(
						array (
								new \ParticipantWorkerPredicate(),
								new \FilterVpdPredicate(),
								new \ParticipantRolePredicate($tasktype_it->get('ProjectRole'))
						)
				)->get('SystemUser');
		}

		// use rules defined on the system level 
		if ( $tasktype_it->get('ParentTaskType') != '' )
		{
			$parent_it = $tasktype_it->getRef('ParentTaskType');
			if ( $parent_it->get('ProjectRole') != '' )
			{
				return getFactory()->getObject('Participant')->getRegistry()->Query(
							array (
									new \ParticipantWorkerPredicate(),
									new \FilterVpdPredicate(),
									new \ParticipantBaseRolePredicate($parent_it->get('ProjectRole'))
							)
					)->get('SystemUser');
			}
		}
		
		// use default rules
		switch($tasktype_it->get('ReferenceName')) 
		{
			case 'development':
			case 'deployment':
			case 'other':
			case 'support':
				$role_kind = 'developer'; 
				break;
				
			case 'testing':
			case 'testdesign':
				$role_kind = 'tester'; 
				break;
				
			case 'analysis':
				$role_kind = 'analyst'; 
				break;
				
			case 'accepting':
				$role_kind = 'client';
				break;
				
			case 'design':
				$role_kind = 'architect'; 
				break;

			case 'documenting':
				$role_kind = 'writer';
				break;
				
			default:
				return '';
		}

		if ( $role_kind == '' ) return '';

		return getFactory()->getObject('Participant')->getRegistry()->Query(
					array (
							new \ParticipantWorkerPredicate(),
							new \FilterVpdPredicate(),
							new \ParticipantBaseRoleNamePredicate($role_kind)
					)
			)->get('SystemUser');
	}
}