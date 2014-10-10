<?php

include "InvitationForm.php";

class InvitationPage extends PMPage
{
 	function needDisplayForm()
 	{
 		return true;
 	}
 	
 	function getForm() 
 	{
 		$_REQUEST['formonly'] = 'true';
 		
 		return new InvitationForm(getFactory()->getObject('Invitation'));
 	}
}