<?php

class UpdateFormDownload extends RecoveryWizardFormBase
{
 	function getAddCaption()
 	{
 		return '1. '.text(1384);
 	}
 	
 	function getCommandClass()
 	{
 		return 'updatedownload';
 	}
 	
 	function getSteps()
 	{
 	    return array( 'total' => 5, 'current' => 1 );
 	}
}
 