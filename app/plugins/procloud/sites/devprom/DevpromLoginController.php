<?php

include( dirname(__FILE__).'/../../crypto/cryptographp.fct.php');

include 'DevpromBaseForm.php';
include 'DevpromLoginForm.php';
include 'DevpromJoinForm.php';
include 'DevpromJoinLicenseForm.php';
include 'DevpromRequestToRestoreForm.php';
include 'DevpromRestoreForm.php';

class DevpromLoginController
{
	function validate()
	{
		global $_REQUEST, $model_factory, $user_it;

		$formmap = array(
			'login' => 'DevpromLoginForm',
			'join' => 'DevpromJoinForm',
			'download' => 'DevpromJoinForm',
			'license' => 'DevpromJoinLicenseForm',
			'restore' => 'DevpromRestoreForm',
			'restorerequest' => 'DevpromRequestToRestoreForm',
		);
		
		if ( !array_key_exists($_REQUEST['action'], $formmap) )
		{
			return false;
		}
		else
		{
			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
			header("Pragma: no-cache"); // HTTP/1.0
			header("Content-Type: text/html; charset=".APP_ENCODING);
	
			$form = new $formmap[$_REQUEST['action']](
				$model_factory->getObject('cms_User'));
				
			$form->draw();
		}
		
		die();
	}
	
	function getKeywords()
	{
	    return array();
	}

	function getTitle()
	{
		return '';
	}		

	function draw()
	{
	}
}
