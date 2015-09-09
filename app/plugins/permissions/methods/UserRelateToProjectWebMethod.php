<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class UserRelateToProjectWebMethod extends WebMethod
{
	var $user_it;

	function UserRelateToProjectWebMethod ( $user_it = null )
	{
		$this->setUser($user_it);
	}

	function setUser( $object_it )
	{
		$this->user_it = $object_it;
	}
	
	function getCaption()
	{
		return translate('Включить в проект');
	}

	function getRedirectUrl()
	{
		return $this->user_it->getViewUrl().'&mode=participant';
	}

	function execute_request()
	{
		global $_REQUEST;
		$this->execute( $_REQUEST );
	}
	
	function execute( $parms )
	{
		global $model_factory;
			
		$user = $model_factory->getObject('cms_User');
		
		$this->user_it = $user->getExact($parms['user']);
			
		if ( $this->user_it->count() > 0 )
		{
			echo $this->getRedirectUrl();
		}
	}

	function hasAccess()
	{
		return getSession()->getUserIt()->IsAdministrator();
	}
}
