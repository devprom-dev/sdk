<?php

use Devprom\CommonBundle\Service\Users\InviteService;

class InvitationForm extends PageForm
{
	private $controller;
	
	function __construct( $object, $controller )
	{
		$this->controller = $controller;
		parent::__construct($object);
	}
	
	function process()
	{
		if ( $this->getAction() != 'add' ) return parent::process();
    	if ( $_REQUEST['Addressee'] == '' ) return;

    	$service = new InviteService($this->controller, getSession());
    	if ( !$service->inviteByEmails($_REQUEST['Addressee']) )
    	{
    		$this->setRequiredAttributesWarning();
    		$this->setWarningMessage(text(2036));
    		$this->edit('');
    		return;
    	}
		$this->redirectOnAdded($this->getObject()->getEmptyIterator());
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
