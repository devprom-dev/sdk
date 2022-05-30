<?php

namespace Devprom\ProjectBundle\Service\Task;

class TaskDefaultsService
{
	static function getAssignee( $type_id )
	{
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
	}
}