<?php

class UpdateFormSystem extends RecoveryWizardFormBase
{
 	function getAddCaption()
 	{
 		return '3. '.text(1383);
 	}
 	
 	function getCommandClass()
 	{
 		return 'updatesystem';
 	}
 	
 	function getSteps()
 	{
 	    return array( 'total' => 3, 'current' => 3 );
 	}
}
 