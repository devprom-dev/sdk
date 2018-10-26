<?php

include_once "UserPasswordHashService.php";

class ReviewLicenseKey extends CommandForm
{
 	function validate()
 	{
		return true;
 	}
 	
 	function create()
	{
		global $model_factory, $_REQUEST, $user_it;

		$license = $model_factory->getObject('LicenseData');
		 
		$license_it = $license->getAll();
		 
		while ( !$license_it->end() )
		{
		    $check_alt_key = $license_it->get('uid') == $_REQUEST['InstallationUID']
		        && $license_it->get('type') == $_REQUEST['LicenseType'];
		     
		    if ( $check_alt_key )
		    {
		        $url = $_REQUEST['Redirect'].'?LicenseType='.$_REQUEST['LicenseType'].
		        	'&LicenseValue='.$license_it->get('value').'&LicenseKey='.$license_it->get('key');
		        	
		        $service = new UserPasswordHashService();
		        
			    $url .= '&UName='.$user_it->get('Caption').
			    		'&UEmail='.$user_it->get('Email').
			    		'&ULogin='.$user_it->get('Login').
			    		'&UPassword='.$service->getInstancePassword($user_it);
		        
		        $this->replyRedirect( $url );
		    }
		
		    $license_it->moveNext();
		}
		
		$this->replyError('Ошибка формирования лицензионного ключа.');
	}
}
