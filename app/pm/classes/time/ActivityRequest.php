<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;

include_once SERVER_ROOT_PATH."pm/classes/time/Activity.php";

class ActivityRequest extends Activity
{
 	function ActivityRequest() 
 	{
 		parent::Activity();
 		
		$strategy = getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy();
		
		if ( $strategy->hasEstimationValue() )
		{
			$this->addAttribute('LeftWork', 'INTEGER', $strategy->getDimensionText(text(1161)), true, false, '', 25);
		}
 	}
 	
	function getTaskIt( $fact, $request_it, $user_id, $task_type )
	{
	 	$task = getFactory()->getObject('pm_Task');
		
	 	$task_it = $task->getRegistry()->Query(
	 			array (
	 					$request_it->get('Tasks') != ''
	 						? new FilterInPredicate($request_it->get('Tasks'))
	 						: new FilterInPredicate(array(-1)),
						new FilterAttributePredicate('TaskType', $task_type)
	 			)
	 	);

		$there_are_tasks = false;
		while ( !$task_it->end() )
		{
			if ( $task_it->get('Assignee') != '' && $task_it->get('Assignee') != $user_id ) {
				$task_it->moveNext();
				continue;
			}
			$there_are_tasks = true;
			break;
		}

		if ( !$there_are_tasks )
		{
 			// create a task
	 		$task->setVpdContext( $request_it );
	 		$task->removeNotificator( 'EmailNotificator' );

	 		$task_id = $task->add_parms(
	 			array (
	 				'Assignee' => $user_id,
	 				'Planned' => $request_it->get('Estimation') > 0 ? $request_it->get('Estimation') : $fact,
	 				'LeftWork' => $request_it->get('EstimationLeft'),
	 				'Fact' => $fact,
					'TaskType' => $task_type != ''
									? $task_type
									: getFactory()->getObject('TaskType')->getRegistry()->Query(
											array (
												new FilterBaseVpdPredicate(),
												new TaskTypeStateRelatedPredicate($request_it->get('State'))
											)
										)->getId(),
	 				'ChangeRequest' => $request_it->getId(),
					'State' => array_pop($task->getTerminalStates())
	 			) );
	 		if ( $task_id <= 0 ) throw new Exception('Unable create task object to be used to store spent time');

	 		$task_it = $task->getExact( $task_id );
		}

		return $task_it;
	}
 	
	function add_parms( $parms )
	{
		$request_it = getFactory()->getObject('pm_ChangeRequest')->getRegistry()->Query(
				array (
						new RequestTasksPersister(),
						new FilterInPredicate($parms['Task'] > 0 ? $parms['Task'] : '-1')
				)
		);
		if ( $request_it->getId() < 1 ) throw new Exception('Request identifier should be passed');

		$this->setVpdContext($request_it);
		
		if ( $parms['Participant'] < 1 ) $parms['Participant'] = getSession()->getUserIt()->getId();

		$task_it = $this->getTaskIt( $parms['Capacity'], $request_it, $parms['Participant'], $parms['TaskType'] );
		$parms['Task'] = $task_it->getId();

		$result = parent::add_parms( $parms );
		if ( $result < 1 ) return $result;

		$request_it->object->removeNotificator( 'EmailNotificator' );
		$request_it->object->modify_parms($request_it->getId(), array(
		        'EstimationLeft' => $parms['LeftWork'] 
		));
		
		return $result;
	}
}