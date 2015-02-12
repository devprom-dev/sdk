<?php

use Devprom\CommonBundle\Service\Emails\RenderService;
include_once SERVER_ROOT_PATH.'cms/c_mail.php';
include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class UserCreatedEvent extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $object_it->object->getEntityRefName() != 'cms_User' ) return;
	    if ( $kind != TRIGGER_ACTION_ADD ) return;

	    $data = $this->getRecordData();

	    $render_service = new RenderService(
	    		getSession(), SERVER_ROOT_PATH."admin/bundles/Devprom/AdministrativeBundle/Resources/views/Emails"
		);
	    
   		$mail = new \HtmlMailbox;
   		$mail->setFromUser(getSession()->getUserIt());
   		$mail->appendAddress($object_it->get('Email'));
   		$mail->setSubject(text(237));
   		$mail->setBody($render_service->getContent('user-registration.twig', 
   				array (
	   				'user_name' => getSession()->getUserIt()->getDisplayName(),
	   				'system_url' => EnvironmentSettings::getServerUrl(),
   					'login' => $object_it->get('Login'),
   					'password' => $data['RepeatPassword'],
   					'reset_url' => EnvironmentSettings::getServerUrl().'/reset?key='.$object_it->getResetPasswordKey().'&redirect=/pm/my'
   				)
   		));
   		
   		$mail->send();
	}
}