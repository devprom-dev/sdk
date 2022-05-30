<?php
use Devprom\CommonBundle\Service\Emails\RenderService;
include_once SERVER_ROOT_PATH.'cms/c_mail.php';

class UserCreatedEvent extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $object_it->object->getEntityRefName() != 'cms_User' ) return;
	    if ( $kind != TRIGGER_ACTION_ADD ) return;

	    $data = $this->getRecordData();

	    $render_service = new RenderService(
	    		getSession(), SERVER_ROOT_PATH."co/bundles/Devprom/CommonBundle/Resources/views/Emails"
		);
	    
   		$mail = new \HtmlMailbox;
   		$mail->appendAddress($object_it->get('Email'));
   		$mail->setSubject(text(237));
   		$mail->setBody($render_service->getContent('user-registration.twig', 
   				array (
	   				'user_name' => $object_it->getDisplayName(),
	   				'system_url' => EnvironmentSettings::getServerUrl(),
   					'login' => $object_it->get('Login'),
   					'password' => $data['RepeatPassword'],
   					'reset_url' => EnvironmentSettings::getServerUrl().'/reset?key='.$object_it->getResetPasswordKey().'&redirect='.$_SERVER['ENTRY_URL'],
                    'docs_url' => EnvironmentSettings::getHelpDocsUrl()
   				)
   		));
   		
   		$mail->send();
	}
}