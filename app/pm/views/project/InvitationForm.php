<?php

class InvitationForm extends PMPageForm
{
    function extendModel()
    {
        parent::extendModel();
        if ( defined('PERMISSIONS_ENABLED') && PERMISSIONS_ENABLED ) {
            $this->getObject()->setAttributeVisible('ProjectRole', true);
            $this->getObject()->setAttributeType('ProjectRole', 'REF_ProjectRoleInheritedId');
        }
    }

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
