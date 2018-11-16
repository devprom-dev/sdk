<?php
include 'ProfileForm.php';
include 'ProfileProjectSection.php';

class ProfilePage extends CoPage
{
 	function __construct()
 	{
 		parent::__construct();

 		if ( defined('PERMISSIONS_ENABLED') ) {
            $this->addInfoSection(
                new ProfileProjectSection(getSession()->getUserIt())
            );
        }
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
