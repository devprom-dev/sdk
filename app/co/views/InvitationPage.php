<?php

include "InvitationForm.php";

class InvitationPage extends CoPage
{
	private $controller = null;
	
	function __construct( $controller )
	{
		$this->controller = $controller;
		parent::__construct();
	}
	
 	function needDisplayForm()
 	{
 		return true;
 	}
 	
 	function getEntityForm()
 	{
 		$_REQUEST['formonly'] = 'true';
 		return new InvitationForm(getFactory()->getObject('Invitation'), $this->controller);
 	}
}