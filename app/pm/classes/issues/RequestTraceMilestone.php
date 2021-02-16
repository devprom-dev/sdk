<?php
include_once "RequestTraceBase.php";
include "RequestTraceMilestoneIterator.php";

class RequestTraceMilestone extends RequestTraceBase
{
 	function __construct()
 	{
 		parent::__construct();
 		
 		$this->setAttributeCaption('ObjectId', translate('Веха'));
        $this->setAttributeType('ObjectId','REF_MilestoneId');

 		$this->addAttribute('Deadline', 'DATE', text(1170), true);
 		$this->addAttribute('DeadlineCaption', 'VARCHAR', text(1171), true);
 	}
 	
 	function getObjectClass() {
 		return 'Milestone';
 	}
 	
 	function createIterator() {
 		return new RequestTraceMilestoneIterator( $this );
 	}
 	
 	function IsAttributeRequired( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'ObjectId':
 				return false;
 			default:
 				return parent::IsAttributeRequired( $attr );
 		}
 	}
 	
	function add_parms( $parms )
	{
		if ( $parms['Deadline'] != '' )
		{
			$milestone = getFactory()->getObject('pm_Milestone');
			$milestone->setVpdContext( $this );
			$milestone_id = $milestone->add_parms( array (
				'MilestoneDate' => $parms['Deadline'], 
				'Caption' => $parms['DeadlineCaption']
			));
			$parms['ObjectId'] = $milestone_id;
		}

		return parent::add_parms($parms);
	}
}