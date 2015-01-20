<?php

class AccountController extends Page
{
	function __construct()
	{
		parent::__construct();
		
		$this->openSession();
	}
	
 	function needDisplayForm() 
 	{
 		return true;
 	}
 	
 	// the page will be available without any authentization required 
 	function authorizationRequired()
 	{
 		return false;
 	}

	protected function openSession()
	{
		$user_it = getFactory()->getObject('User')->getRegistry()->Query(
				array (
						new FilterAttributePredicate('Email', $_REQUEST['Email']),
						new FilterInstallationUIDPredicate($_REQUEST['InstallationUID'])
				)
		);

		if ( $user_it->getId() < 1 ) return false;
		
		getSession()->open($user_it);
		
		return true;
	}
}
