<?php

class AccountController extends Page
{
	function __construct()
	{
		parent::__construct();
		
		$this->openSession();
	}
	
 	function needDisplayForm() 
 	{
 		return true;
 	}
 	
 	// the page will be available without any authentization required 
 	function authorizationRequired()
 	{
 		return false;
 	}

	protected function openSession()
	{
		$user = getFactory()->getObject('User');
		$user_it = $user->getRegistry()->Query(
				array (
						new FilterAttributePredicate('Email', strtolower(trim($_REQUEST['Email']))),
						new FilterInstallationUIDPredicate(trim($_REQUEST['InstallationUID']))
				)
		);

		if ( $user_it->getId() < 1 ) {
			$user_it = $user->getRegistry()->Query(
					array (
							new FilterAttributePredicate('Email', strtolower(trim($_REQUEST['Email'])))
					)
			);
			if ( $user_it->getId() != '' ) return false; // authorization required
			
			$user_it = $this->joinCustomer( 
					$_REQUEST['UserName'], 
					$_REQUEST['Email'], 
					$_REQUEST['Language'],
					$_REQUEST['InstallationUID'], 
					$_REQUEST['LicenseType']
			);
		}

		getSession()->open($user_it);

		getSession()->resetLanguage();
		getSession()->configure();
		getSession()->getLanguage();
		
		return true;
	}

	protected function joinCustomer( $name, $email, $language, $uid, $type )
	{
		$user = getFactory()->getObject('User');
		$user->setNotificationEnabled(false);
		
		$user_it = $user->getRegistry()->Query(
				array (
						new FilterAttributePredicate('Email', $email),
						new FilterInstallationUIDPredicate($uid)
				)
		);
		
		if ( $user_it->getId() == '' )
		{
			$user_id = $user->add_parms(
					array (
							'Caption' => IteratorBase::utf8towin($name),
							'Email' => $email,
							'Password' => $this->generatePassword(),
							'Login' => array_shift(preg_split('/@/', $email)),
							'Language' => $language
					)
			);
			$user_it = $user->getExact($user_id);

			getFactory()->getObject('AccountLicenseData')->modify_parms( $user_it->getId(),
					array (
							'uid' => $uid,
							'type' => $type
					)
			);
		}
		
		return $user_it;
	}
	
	protected function generatePassword()
	{
		return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.-+{}[]()"),0,16);
	}
}
