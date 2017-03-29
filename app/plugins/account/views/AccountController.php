<?php

class AccountController extends Page
{
	function __construct()
	{
        $this->openSession();
		parent::__construct();
	}
	
 	function needDisplayForm() 
 	{
 		return true;
 	}
 	
 	// the page will be available without any authentication required
 	function authorizationRequired()
 	{
 		return false;
 	}

	protected function openSession()
	{
		$user = getFactory()->getObject('User');
        $email = strtolower(trim($_REQUEST['Email']));
        if ( $email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
            $user_it = $user->createCachedIterator(
                array(
                    array (
                        'cms_UserId' => 1,
                        'Caption' => $_REQUEST['UserName'],
                        'Email' => $email,
                        'Language' => $_REQUEST['Language'],
                        'InstallationUID' => $_REQUEST['InstallationUID'],
                        'ICQ' => 'dummy'
                    )
                )
            );
        }
        else {
            $user_it = $user->getRegistry()->Query(
                    array (
                            new FilterAttributePredicate('Email', $email),
                            new FilterInstallationUIDPredicate(trim($_REQUEST['InstallationUID']))
                    )
            );
            if ( $user_it->getId() < 1 ) {
                $user_it = $user->getRegistry()->Query(
                    array (
                        new FilterAttributePredicate('Email', $email)
                    )
                );
                if ( $user_it->getId() == '' ) {
                    $user_it = $this->joinCustomer(
                            $_REQUEST['LicenseScheme'] == 2 ? IteratorBase::wintoutf8($_REQUEST['UserName']) : $_REQUEST['UserName'],
                        $email,
                        $_REQUEST['Language'],
                        $_REQUEST['InstallationUID'],
                        $_REQUEST['LicenseType']
                    );
                }
            }
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
	
	protected function generatePassword() {
		return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.-+{}[]()"),0,16);
	}
}
