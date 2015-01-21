<?php

include "SetupLicenseForm.php";

class SetupLicenseController extends Page
{
 	function needDisplayForm() 
 	{
 		return true;
 	}
 	
 	function render()
 	{
 		$license = getFactory()->getObject('LicenseInstalled');
 		$license->modify_parms($license->getAll()->getId(),
 				array (
 						'LicenseValue' => $_REQUEST['LicenseValue'],
 						'LicenseKey' => $_REQUEST['LicenseKey']
 				)
 		);
 		
 		parent::render();
 	}
 	
 	function getForm()
 	{
 		return new SetupLicenseForm(getFactory()->getObject('License'));
 	}
}
