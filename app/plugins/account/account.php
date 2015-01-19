<?php

include "classes/AccountSiteJSBuilder.php";
include "classes/AccountSiteCssBuilder.php";
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
 	
  	function getHeaderMenus()
 	{
 		$license_it = getFactory()->getObject('LicenseInstalled')->getAll();
 		
 		$buy_url = '/module/account/form?'.http_build_query(
 				array (
 						'LicenseType' => $license_it->get('LicenseType'),
 						'WasLicenseValue' => $license_it->get('LicenseValue'),
 						'WasLicenseKey' => $license_it->get('LicenseKey'),
 						'InstallationUID' => INSTALLATION_UID
 				)
 			);
 				
 		return array(
 				array (
 						'caption' => 'Оплатить',
 						'class' => 'btn-success',
 						'url' => "javascript: showAccountForm('".$buy_url."');"
 				),
 				array('class' => 'empty'),
 				array('class' => 'empty'),
 				array('class' => 'empty')
 		);
 	}
}