<?php

class RecoveryFormApplication extends RecoveryWizardFormBase
{
 	function getAddCaption()
 	{
 		return '3. '.translate('�������������� ����������');
 	}
 	
 	function getCommandClass()
 	{
 		return 'recoveryapplication';
 	}

 	function getSteps()
 	{
 	    return array( 'total' => 4, 'current' => 3 );
 	}
}
 