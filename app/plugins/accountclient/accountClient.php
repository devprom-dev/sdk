<?php

include "classes/AccountSiteJSBuilder.php";
include "classes/AccountSiteCssBuilder.php";
include "AdminPlugin.php";
include "COPlugin.php";
include "PMPlugin.php";

// define common plugin attributes
class accountClientPlugin extends PluginBase
{
    const SERVER_URL = 'https://myalm.ru';

	// this is plugin's unique internal name
 	function getNamespace()
 	{
 		return 'accountclient';
 	}
 
 	// 
  	function getFileName()
 	{
 		return 'accountClient.php';
 	}
 	
 	// plugin's title
 	function getCaption()
 	{
 		return text('accountclient1');
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
 		return array( new accountClientCo, new accountClientAdmin, new accountClientPM );
 	}
 	
 	function IsUpdatedWithCore()
 	{
 		return false;
 	}

  	function getHeaderMenus()
 	{
 		$user_it = getSession()->getUserIt();
 		if ( !$user_it->IsAdministrator() ) return array();

 		$license_it = getFactory()->getObject('LicenseInstalled')->getAll();

        $left_days = $license_it->get('LeftDays');
        if ( $left_days == '' )	$left_days = $license_it->getLeftDays();
        if ( $left_days == '' ) $left_days = 0;

        if ( $left_days > 7 ) return array();

		switch ( $license_it->get('LicenseType') ) {
			case 'LicenseTeam':
				return array();
			default:
				$button_text = text('accountclient2');
				break;
		}

 		return array(
 				array('class' => 'empty'),
 				array('class' => 'empty'),
				array('class' => 'empty'),
 				array('class' => 'empty'),
  				array (
 						'caption' => str_replace('%1', $left_days.' '.getSession()->getLanguage()->getDaysWording($left_days), $button_text),
 						'class' => 'btn-success',
 						'url' => "javascript: ".urlencode(AccountSiteJSBuilder::getScriptToBuy()).";",
 						'icon' => 'icon-white icon-shopping-cart'
 				),
 				array('class' => 'empty'),
 				array('class' => 'empty'),
 				array('class' => 'empty')
 		);
 	}
}