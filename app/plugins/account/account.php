<?php

define ('ACCOUNT_HOST', 'https://devprom.ru');

include "classes/model/AccountProduct.php";
include "classes/model/AccountProductSaas.php";
include "classes/model/AccountProductDevOps.php";
include "classes/model/AccountProductSupport.php";
include "classes/model/AccountLicenseData.php";
include "classes/model/ServicePayed.php";
include "classes/model/predicates/FilterInstallationUIDPredicate.php";
include "COPlugin.php";
include "AdminPlugin.php";

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
 		return array( new accountCo, new accountAdmin );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}
}