<?php

include_once "AccountController.php";
include "LicenseForm.php";
include "RegistrationForm.php";

class AccountFormController extends AccountController
{
 	function getForm()
 	{
 	    if ( getSession()->getUserIt()->get('ICQ') == 'dummy' ) {
            return new RegistrationForm(getFactory()->getObject('User'));
        }
        else {
            return new LicenseForm(getFactory()->getObject('License'));
        }
 	}
}
