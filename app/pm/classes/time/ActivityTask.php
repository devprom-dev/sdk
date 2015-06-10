<?php

include_once SERVER_ROOT_PATH."pm/classes/time/Activity.php";

class ActivityTask extends Activity
{
 	function __construct() 
 	{
 		parent::__construct();

 		$this->addAttribute('LeftWork', 'INTEGER', translate('Осталось, ч.'), true);
 	}
 	
	function add_parms( $parms )
	{
		$task_it = getFactory()->getObject('Task')->getRegistry()->Query(
				array (
						new FilterInPredicate($parms['Task'])
				)
		);
		
		if ( $task_it->getId() < 1 ) throw new Exception('Task identifier should be passed');

		$this->setVpdContext($task_it);
		
		if ( $parms['Participant'] < 1 ) $parms['Participant'] = getSession()->getUserIt()->getId();

		return parent::add_parms( $parms );
	}
}
