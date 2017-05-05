<?php

namespace Devprom\CommonBundle\Service\Users;

class InviteService
{
	public function __construct( $controller, $session )
	{
		$this->controller = $controller;
		$this->session = $session;
	}
	
	public function inviteByEmails( $emails )
	{
		if ( !is_array($emails) ) {
			$emails = array_filter(
					preg_split('/[,\s;\r\n]/', $emails),
					function($value) {
							return $value != '' && filter_var(trim($value), FILTER_VALIDATE_EMAIL) !== false;
					}
	        );
		}
		if ( count($emails) < 1 ) throw new \Exception(text(2158));

		$user = getFactory()->getObject('User'); 
		if ( !getFactory()->getAccessPolicy()->can_create($user) ) return false;

		foreach( $emails as $email )
		{
			$email = trim(strtolower($email));
			$user_it = $user->getRegistry()->Query(
					array (
							new \FilterAttributePredicate('Email', $email)
					)
			);
			if ( $user_it->getId() > 0 ) continue;
			
			list($login, $domain) = preg_split('/@/', $email);
			$password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.-+{}[]()"),0,16);
			
			$user->add_parms(
					array (
 							'Caption' => $login,
	 						'Login' => $login,
 							'Email' => $email,
 							'Password' => $password,
 							'RepeatPassword' => $password,
 							'Language' => getFactory()->getObject('SystemSettings')->getAll()->get('Language')
					)
			);
		}

        getFactory()->getCacheService()->truncate('sessions');

		return true;
	}
	
	private $controller = null;
	private $session = null;
}