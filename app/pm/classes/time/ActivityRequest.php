<?php
use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once SERVER_ROOT_PATH."pm/classes/time/Activity.php";

class ActivityRequest extends Activity
{
 	function __construct( ObjectRegistry $registry = null )
 	{
 		parent::__construct($registry);

		$strategy = new EstimationHoursStrategy();
		$this->addAttribute('LeftWork', 'INTEGER', $strategy->getDimensionText(text(1161)), true, false, '', 15);
        $this->addAttributeGroup('LeftWork', 'hours');
        $this->addAttributeGroup('LeftWork', 'workload');
		$this->setAttributeRequired('Issue', true);
        $this->setAttributeRequired('Task', false);
 	}
 	
	function getTaskIt( $request_it, $user_id, $task_type )
	{
	 	$task = getFactory()->getObject('pm_Task');
		
	 	$task_it = $task->getRegistry()->Query(
            array (
                new FilterAttributePredicate('ChangeRequest', $request_it->getId()),
                new FilterAttributePredicate('TaskType', $task_type),
                new FilterAttributePredicate('Assignee', $user_id)
            )
	 	);
        if ( $task_it->count() > 0 ) return $task_it;

        // create a task
        $task->setVpdContext( $request_it );
        $task->removeNotificator( 'EmailNotificator' );

        $registry = getFactory()->getObject('TaskType')->getRegistry();
        $default_task_type = $registry->Query(
                array (
                    new FilterBaseVpdPredicate(),
                    new TaskTypeStateRelatedPredicate($request_it->get('State'))
                )
            )->getId();
        if ( $default_task_type == '' ) {
            $default_task_type = $registry->Query(
                array (
                    new FilterBaseVpdPredicate(),
                    new FilterAttributePredicate('IsDefault', 'Y')
                )
            )->getId();
        }
        if ( $default_task_type == '' ) {
            $default_task_type = $registry->Query(
                array (
                    new FilterBaseVpdPredicate(),
                    new FilterAttributePredicate('ReferenceName', 'development')
                )
            )->getId();
        }

        $task_id = $task->add_parms(
            array (
                'Assignee' => $user_id,
                'LeftWork' => $request_it->get('EstimationLeft'),
                'TaskType' => $task_type != '' ? $task_type : $default_task_type,
                'ChangeRequest' => $request_it->getId(),
                'State' => array_pop($task->getTerminalStates())
            ) );
        if ( $task_id <= 0 ) throw new Exception('Unable create task object to be used to store spent time');

		return $task->getExact( $task_id );
	}
 	
	function add_parms( $parms )
	{
		$request_it = getFactory()->getObject('pm_ChangeRequest')->getRegistry()->Query(
            array (
                new RequestTasksPersister(),
                new FilterInPredicate($parms['Issue'] > 0 ? $parms['Issue'] : '-1')
            )
		);
        if ( $request_it->getId() != '' ) {
            $this->setVpdContext($request_it);
        }

		if ( $parms['Participant'] < 1 ) {
            $parms['Participant'] = getSession()->getUserIt()->getId();
        }

        if ( $parms['Task'] == '' ) {
            $task_it = $this->getTaskIt( $request_it, $parms['Participant'], $parms['TaskType'] );
            $parms['Task'] = $task_it->getId();
        }
        else {
            $task_it = getFactory()->getObject('Task')->getExact($parms['Task']);
        }
        $this->setVpdContext($task_it);

		$result = parent::add_parms( $parms );
		if ( $result < 1 ) return $result;

		if ( $parms['LeftWork'] != '' ) {
            $request_it->object->removeNotificator( 'EmailNotificator' );
            $request_it->object->modify_parms($request_it->getId(), array(
                'EstimationLeft' => $parms['LeftWork']
            ));
        }

		return $result;
	}
}