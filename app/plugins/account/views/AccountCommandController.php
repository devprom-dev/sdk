<?php

include_once "AccountController.php";
include "services/GetLicenseKey.php";
include "services/ProcessOrder.php";
include "services/ProcessOrder2.php";

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

 		    case 'processorder':
				$order_info = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
				if ( is_numeric($order_info['WasLicenseValue']) ) {
					$command = new ProcessOrder();
				} else {
					$command = new ProcessOrder2();
				}
 		    	$command->execute();
 		    	break; 
 		}
 	}
}
