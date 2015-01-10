<?php

include SERVER_ROOT_PATH."core/c_command.php";
include SERVER_ROOT_PATH."plugins/account/commands/GetLicenseKey.php";

class AccountCommandController extends Page
{
 	function needDisplayForm() 
 	{
 		return false;
 	}
 	
 	// the page will be available without any authentization required 
 	function authorizationRequired()
 	{
 		return false;
 	}
 	
 	function render()
 	{
 		global $user_it;
 		
 		$user_it = getSession()->getUserIt();
 		
 		switch( $_REQUEST['name'] )
 		{
 		    case 'getlicensekey':
 		    	$command = new GetLicenseKey();
 		    	$command->execute();
 		    	break; 
 		}
 	}
}
