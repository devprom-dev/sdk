<?php
include_once SERVER_ROOT_PATH."pm/classes/time/Activity.php";

class ActivityTask extends Activity
{
 	function __construct(ObjectRegistry $registry = null)
 	{
 		parent::__construct($registry);
 		$this->addAttribute('LeftWork', 'INTEGER', translate('Осталось'), true, false, '', 15);
        $this->addAttributeGroup('LeftWork', 'hours');
        $this->addAttributeGroup('LeftWork', 'workload');
        $this->setAttributeRequired('Task', true);
        $this->setAttributeRequired('Issue', false);
 	}
 	
	function add_parms( $parms )
	{
		$task_it = getFactory()->getObject('Task')->getRegistry()->Query(
            array (
                new FilterInPredicate($parms['Task'] > 0 ? $parms['Task'] : '-1')
            )
		);
		if ( $task_it->getId() < 1 ) throw new Exception('Task identifier should be passed');
		$this->setVpdContext($task_it);
		
		if ( $parms['Participant'] < 1 ) {
            $parms['Participant'] = getSession()->getUserIt()->getId();
        }

        $methodologyIt = getSession()->getProjectIt()->getMethodologyIt();
        if ( !$methodologyIt->TaskEstimationUsed() && $methodologyIt->IsEstimationHoursStrategy() ) {
            $requestIt = $task_it->getRef('ChangeRequest');
            if ( $requestIt->getId() != '' ) {
                $requestIt->object->removeNotificator( 'EmailNotificator' );
                $requestIt->object->modify_parms(
                    $requestIt->getId(),
                    array(
                        'EstimationLeft' => $parms['LeftWork']
                    )
                );
            }
        }

		return parent::add_parms( $parms );
	}
}
