<?php
include 'ProfileForm.php';

class ProfilePage extends CoPage
{
 	function getTable()
 	{
 	    $user = getFactory()->getObject('User');
        $user->addAttributeGroup('Password', 'system');
        $user->addAttributeGroup('IsReadonly', 'system');

		return new ProfileForm(
            $user->getExact(
		        getSession()->getUserIt()->getId()
            )
        );
 	}
 	
 	function getTitle() {
 		return translate('Профиль');
 	}
}
