<?php

class RecoveryFormUnpack extends RecoveryWizardFormBase
{
 	function getAddCaption()
 	{
 		return '1. '.translate('�������� ������ ��������� �����');
 	}
 	
 	function getCommandClass()
 	{
 		return 'recoveryunpack';
 	}
 	
 	function getSteps()
 	{
 	    return array( 'total' => 4, 'current' => 1 );
 	}
}
 