<?php

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
 	
	function getTaskIt( $fact, $request_it, $part_it )
	{
	 	$task = getFactory()->getObject('pm_Task');
		
	 	$task_it = $task->getRegistry()->Query(
	 			array (
	 					$request_it->get('OpenTasks') != ''
	 						? new FilterInPredicate($request_it->get('OpenTasks'))
	 						: new FilterInPredicate(array(-1))
	 			)
	 	);
		
		$there_are_tasks = false;
		
		while ( !$task_it->end() )
		{
			if ( $task_it->get('Assignee') != '' && $task_it->get('Assignee') != $part_it->getId() )
			{
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
	 		$task->removeNotificator( 'ChangesWaitLockReleaseTrigger' );

	 		$task_id = $task->add_parms(
	 			array (
	 				'Assignee' => $part_it->getId(),
	 				'Planned' => $request_it->get('Estimation') > 0 ? $request_it->get('Estimation') : $fact,
	 				'LeftWork' => $request_it->get('EstimationLeft'),
	 				'Fact' => $fact,
	 				'ChangeRequest' => $request_it->getId(),
	 				'TaskType' => 
	 					getFactory()->getObject('TaskType')->getRegistry()->Query( 
	 							array ( 
	 									new FilterAttributePredicate('ReferenceName', 'development'),
	 									new FilterBaseVpdPredicate()
	 							)
	 						)->getId()
	 			) );
	 			
	 		if ( $task_id <= 0 ) throw new Exception('Unable create task object will be used to store spent time');
	 		
	 		$task_it = $task->getExact( $task_id );
	 		
	 		$state_it = $request_it->getStateIt();
	 		
	 		if ( $state_it->get('IsTerminal') == 'Y' )
	 		{
	 			$states = $task_it->object->getTerminalStates();
	 			
	 			$task_it->modify( array (
	 				'State' => $states[0] != '' ? $states[0] : 'resolved',
	 				'Result' => text(1013),
	 				'LeftWork' => 0
	 			));
	 		}
		}
		else
		{
			$task_it->moveFirst();
		}
		
		return $task_it;
	}
 	
	function add_parms( $parms )
	{
		$request_it = getFactory()->getObject('pm_ChangeRequest')->getRegistry()->Query(
				array (
						new FilterInPredicate($parms['Task'] > 0 ? $parms['Task'] : '-1')
				)
		);

		if ( $request_it->getId() < 1 ) throw new Exception('Request identifier should be passed');

		$this->setVpdContext($request_it);
		
		$participant = getFactory()->getObject('Participant');
		
		$participant->setVpdContext( $request_it );
		
		if ( $parms['Participant'] < 1 )
		{
    		$participant_it = $participant->getByRef('SystemUser', getSession()->getUserIt()->getId());
		
    		$parms['Participant'] = $participant_it->getId();
		}
		else
		{
		    $participant_it = $participant->getExact($parms['Participant']);
		}

    	if ( $participant_it->getId() < 1 ) throw new Exception('Participant identifier should be passed');
		
		$task_it = $this->getTaskIt( $parms['Capacity'], $request_it, $participant_it );
		
		$parms['Task'] = $task_it->getId(); 

		$request_it->object->removeNotificator( 'ChangesWaitLockReleaseTrigger' );
		$request_it->object->removeNotificator( 'EmailNotificator' );
		
		$request_it->modify( array(
		        'EstimationLeft' => $parms['LeftWork'] 
		));
		
		return parent::add_parms( $parms );
	}
}