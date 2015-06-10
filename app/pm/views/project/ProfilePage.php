<?php

include "ProfileForm.php";

class ProfilePage extends PMPage
{
	private $policy = null;
	
 	function __construct()
 	{
 		$this->policy = getFactory()->getAccessPolicy();
 		
 		getFactory()->setAccessPolicy( new AccessPolicy(new CacheEngine()) );

 		parent::__construct();

 		getFactory()->setAccessPolicy( $this->policy );
 	}
 	
 	function getTable() 
	{
		return null;		
 	}
 	
 	function needDisplayForm()
 	{
 		return true;
 	}
 	
	function draw()
	{
 		getFactory()->setAccessPolicy( new AccessPolicy(new CacheEngine()) );
		 		
 		parent::draw();

 		getFactory()->setAccessPolicy( $this->policy );
	}
	
 	function getForm() 
 	{
 		global $model_factory, $_REQUEST, $part_it;
 		
		$_REQUEST['pm_ParticipantId'] = $part_it->getId();
		
		if ( $_REQUEST['pm_Participantaction'] != 'modify' )
		{
			$_REQUEST['pm_Participantaction'] = 'show';
		}

		return new ProfileForm(getFactory()->getObject('pm_Participant'));
 	}
}
