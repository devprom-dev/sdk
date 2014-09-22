<?php

include_once "AdminEmailNotificatorHandler.php";

class AdminUserHandler extends AdminEmailNotificatorHandler
{
	function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		if ( $action != 'add' ) return array();
		
		return array( $object_it->get('Email') );
	}	
	
	function getSubject( $subject, $object_it, $prev_object_it, $action, $recipient )
	{
		if ( $action != 'add' ) return '';
		
		return text(237);
	}

 	function getSender( $object_it, $action ) 
 	{
 		$user_it = getSession()->getUserIt();
 		
		return '"'.trim($user_it->get('Caption'), '"').'" <'.$user_it->get('Email').'>';
 	}
	
	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		if ( $action != 'add' ) return '';
		
		$body = file_get_contents(
					preg_replace('/%lang%/', strtolower(getLanguage()->getLanguage()), 
							SERVER_ROOT_PATH.'templates/resources/%lang%/user-registration.html')
		);

		$body = str_replace('%6', 
			_getServerUrl().'/reset?key='.$object_it->getResetPasswordKey().'&redirect=/pm/my', $body);

		$body = str_replace('%7', _getServerUrl().'/co/profile.php', $body);

		$body = str_replace('%1', $object_it->getDisplayName(), $body);
		$body = str_replace('%2', $object_it->get('Login'), $body);
		$body = str_replace('%3', $_REQUEST['PasswordOriginal'], $body);
		$body = str_replace('%5', _getServerUrl(), $body);
		
		return $body;
	}	
}
