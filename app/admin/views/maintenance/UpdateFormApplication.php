<?php

class UpdateFormApplication extends RecoveryWizardFormBase
{
 	function getAddCaption()
 	{
 		return '2. '.text(1737);
 	}
 	
 	function getCommandClass()
 	{
 		return 'updateapplication';
 	}
 	
 	function getSteps()
 	{
 	    return array( 'total' => 3, 'current' => 2 );
 	}
}
 