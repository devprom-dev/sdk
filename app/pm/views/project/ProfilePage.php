<?php

include "ProfileForm.php";

class ProfilePage extends PMPage
{
	private $policy = null;
	
 	function __construct()
 	{
 		$this->policy = getFactory()->getAccessPolicy();
 		
 		getFactory()->setAccessPolicy( new AccessPolicy(CacheEngineVar::Instance(), getSession()->getCacheKey()) );

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
 		getFactory()->setAccessPolicy( new AccessPolicy(CacheEngineVar::Instance(),getSession()->getCacheKey()) );
		 		
 		parent::draw();

 		getFactory()->setAccessPolicy( $this->policy );
	}
	
 	function getEntityForm()
 	{
		$object = getFactory()->getObject('pm_Participant');
		$form = new ProfileForm($object);
		$form->edit(getSession()->getParticipantIt());
		return $form;
 	}
}
