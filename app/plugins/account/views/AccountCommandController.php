<?php

include SERVER_ROOT_PATH."core/c_command.php";
include SERVER_ROOT_PATH."plugins/account/commands/GetLicenseKey.php";
include_once "AccountController.php";

class AccountCommandController extends AccountController
{
 	function needDisplayForm() 
 	{
 		return false;
 	}
 	
 	function render()
 	{
 		global $user_it;
 		
 		$user_it = getSession()->getUserIt();
 		
 		header('Access-Control-Allow-Origin: *');
 		header('Access-Control-Allow-Methods: *');
 		header('Access-Control-Allow-Headers: *');
		
 		switch( $_REQUEST['name'] )
 		{
 		    case 'getlicensekey':
 		    	$command = new GetLicenseKey();
 		    	$command->execute();
 		    	break; 
 		}
 	}
}
