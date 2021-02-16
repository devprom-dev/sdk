<?php
include 'ProfileNotificationsForm.php';
include 'ProfileProjectSection.php';

class ProfileNotificationsPage extends CoPage
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
 	    $user = getFactory()->getObject('User');
        $user->addAttributeGroup('Password', 'system');
        $user->addAttributeGroup('IsReadonly', 'system');

		return new ProfileNotificationsForm(
            $user->getExact(
		        getSession()->getUserIt()->getId()
            )
        );
 	}
 	
 	function getTitle() {
 		return text(1912);
 	}
}
