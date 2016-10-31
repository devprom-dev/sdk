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

		try {
			$service = new InviteService($this->controller, getSession());
			if ( !$service->inviteByEmails($_REQUEST['Addressee']) ) {
				throw new \Exception(text(2036));
			}
			$this->redirectOnAdded($this->getObject()->createCachedIterator(
				array (
					array (
						$this->getObject()->getIdAttribute() => 1
					)
				)
			));
		}
		catch( \Exception $e )
    	{
    		$this->setRequiredAttributesWarning();
    		$this->setWarningMessage($e->getMessage());
    		$this->edit('');
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
