<?php

define ('ACCOUNT_HOST', 'http://devprom.ru');
//define ('MERCHANT_ID', 7742);
//define ('MERCHANT_KEY', 'efef1ce9-6c7b-401f-8430-1e96540636fc');
define ('MERCHANT_ID', 62021);
define ('MERCHANT_KEY', '30cfcab4-ce10-413f-bbfd-4a367823bc1c');

include "classes/model/AccountProduct.php";
include "classes/model/AccountProductSaas.php";
include "classes/model/AccountLicenseData.php";
include "classes/model/predicates/FilterInstallationUIDPredicate.php";
include "COPlugin.php";

// define common plugin attributes
class accountPlugin extends PluginBase
{
	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'account';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'account.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('account1');
 	}
 	
 	// plugin's order number in the list of plugins, is using to define dependencies between plugins
 	function getIndex()
 	{
 	    return parent::getIndex() + 5000;
 	}
 	
 	// returns plugins extentions for the corresponding application:
 	// PM - project management
 	// Admin - administration
 	// CO - common section
 	//
 	function getSectionPlugins()
 	{
 		return array( new accountCo );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}