<?php

class SettingsWebMethod extends WebMethod
{
 	function execute_request() 
 	{
 		global $_REQUEST, $model_factory;
 		
 		if ( $_REQUEST['setting'] == '' ) return;
 		
 		if ( $_REQUEST['value'] == '' ) return;
 		
 		$settings = $model_factory->getObject('UserSettings');
 		
 		$settings->setSettingsValue($_REQUEST['setting'], $_REQUEST['value']);
 	}
}
