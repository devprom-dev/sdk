<?php

class UpdateFormCheck extends RecoveryWizardFormBase
{
 	function getAddCaption()
 	{
 		return '2. '.text(1385);
 	}
 	
 	function getCommandClass()
 	{
 		return 'updateupload';
 	}
 	
 	function getSteps()
 	{
 	    return array( 'total' => 5, 'current' => 2 );
 	}
}
 