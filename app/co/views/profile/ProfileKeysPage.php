<?php
include 'ProfileKeysForm.php';

class ProfileKeysPage extends CoPage
{
 	function getTable()
 	{
 	    $user = getFactory()->getObject('User');
        $user->addAttributeGroup('Password', 'system');
        $user->addAttributeGroup('IsReadonly', 'system');

		return new ProfileKeysForm(
            $user->getExact(
		        getSession()->getUserIt()->getId()
            )
        );
 	}
 	
 	function getTitle() {
 		return text(2913);
 	}
}
