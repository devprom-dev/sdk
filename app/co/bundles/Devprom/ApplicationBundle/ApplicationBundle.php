<?php

namespace Devprom\ApplicationBundle;

use Devprom\Component\HttpKernel\Bundle\DevpromBundle;
use Devprom\ApplicationBundle\Service\Mailer\MailerLogger;
use Swift_Plugins_LoggerPlugin;

include_once SERVER_ROOT_PATH.'core/methods/WebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/ProcessEmbeddedWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/DeleteEmbeddedWebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/SettingsWebMethod.php';
include_once SERVER_ROOT_PATH.'/core/c_command.php';

class ApplicationBundle extends DevpromBundle
{
	public function boot()
    {
    	parent::boot();
		$this->setUpMailLogging();

    	if ( $this->handleCustomMethod() ) die();
    	if ( $this->handleCustomCommand() ) die();
    }
    
    protected function handleCustomMethod()
    {
        if ( $_REQUEST['method'] == '' ) return false;
        if ( !class_exists($_REQUEST['method'], false) ) return false;
        
    	$method = new $_REQUEST['method'];
        FeatureTouch::Instance()->touch(strtolower(get_class($method)));
        $method->exportHeaders();
        $method->execute_request();
        
        return true;
    }
    
    protected function handleCustomCommand()
    {
	    $class = $_REQUEST['class'];
		if ( $class == 'metaobject' ) return false;

		$page = \SanitizeUrl::parseSystemUrl($_REQUEST['redirect']);

		if ( preg_match('/^[a-zA-Z0-9]+$/im', $class) < 1 ) unset($class);
		if ( !isset($class) ) return false;

		$module = SERVER_ROOT_PATH.'tasks/commands/c_'.$class.'.php';
		if ( !class_exists($class, false) && file_exists($module) ) include_once $module;

		$module = SERVER_ROOT_PATH.'co/commands/c_'.$class.'.php';
		if ( !class_exists($class, false) && file_exists($module) ) include_once $module;

		if ( class_exists($class, false) ) {
		 	$command = new $class;	
		}
		else {
		 	$command = \PluginsFactory::Instance()->getCommand( $_REQUEST['namespace'], 'co', $class );
		}

		if ( is_object($command) ) $command->execute();

		if ( $page != '' ) {
			exit(header('Location: '.$page));
		}
		return true;
    }

	protected function setUpMailLogging()
	{
		$mailer = $this->container->get('mailer');
		$mailer->registerPlugin(
			new Swift_Plugins_LoggerPlugin($this->container->get('mail_transport_logger'))
		);
		$mailer->registerPlugin($this->container->get('message_logger'));
	}
}
