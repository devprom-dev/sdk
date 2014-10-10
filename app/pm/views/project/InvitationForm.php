<?php

class InvitationForm extends PMPageForm
{
	function getRenderParms()
	{
		$parms = parent::getRenderParms();
		
		$parms['button_save_title'] = translate('Пригласить');
		
		return $parms;
	}
	
	function getFormPage()
	{
		return '?';
	}
}
