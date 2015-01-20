<?php

include_once "AccountController.php";
include "LicenseForm.php";

class AccountFormController extends AccountController
{
 	function getForm()
 	{
 		return new LicenseForm(getFactory()->getObject('License'));
 	}
}
