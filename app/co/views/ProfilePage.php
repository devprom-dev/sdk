<?php

include ('ProfileForm.php');

class ProfilePage extends CoPage
{
 	function __construct()
 	{
 		parent::__construct();
   	}
			
 	function getTable()
 	{
		return new ProfileForm( getSession()->getUserIt() );
 	}
 	
 	function getTitle()
 	{
 		return translate('Профиль');
 	}
}
