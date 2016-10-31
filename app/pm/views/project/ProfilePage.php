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
		$object = getFactory()->getObject('pm_Participant');
		$objectIt = $object->getExact(getSession()->getParticipantIt()->getId());

		if ( getSession()->getProjectIt()->IsPortfolio() ) {
			$objectIt = $object->createCachedIterator(array(
				array (
					'pm_ParticipantId' => PHP_INT_MAX
				)
			));
		}

		$form = new ProfileForm($object);
		$form->edit($objectIt);
		return $form;
 	}
}
