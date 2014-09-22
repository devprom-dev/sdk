<?php

class RecoveryFormFiles extends RecoveryWizardFormBase
{
 	function getAddCaption()
 	{
 		return '2. '.text(1513);
 	}
 	
 	function getCommandClass()
 	{
 		return 'recoveryfiles';
 	}
 	
 	function getSteps()
 	{
 	    return array( 'total' => 4, 'current' => 2 );
 	}
}
 