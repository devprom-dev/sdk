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
		return new ProfileForm(
		    getFactory()->getObject('User')->getExact(
		        getSession()->getUserIt()->getId()
            )
        );
 	}
 	
 	function getTitle() {
 		return translate('Профиль');
 	}
}
