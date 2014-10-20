<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ScriptBuilder.php";

class ScriptIntercomBuilder extends ScriptBuilder
{
	private $session = null;
	
	function __construct( $session )
	{
		$this->session = $session;
	}
	
    public function build( ScriptRegistry & $object )
    {
    	if ( getFactory()->getObject('LicenseState')->getAll()->get('LicenseType') == 'LicenseProcloud' ) return;
    	
    	$user_it = $this->session->getUserIt();
    	
    	if ( $user_it->getId() < 1 ) return;
    	
    	$content = file_get_contents(SERVER_ROOT_PATH."plugins/saasassist/resources/intercom.js");
    	
    	$content = preg_replace('/%name%/', IteratorBase::wintoutf8($user_it->getDisplayName()), $content);  
    	$content = preg_replace('/%email%/', $user_it->get('Email'), $content);
    	$content = preg_replace('/%date%/', (new DateTime($user_it->get('RecordCreated')))->getTimestamp(), $content);
    	$content = preg_replace('/%projects%/', getFactory()->getObject('Project')->getRegistry()->Count(), $content);
    	$content = preg_replace('/%users%/', getFactory()->getObject('User')->getRegistry()->Count(), $content);
    	$content = preg_replace('/%host%/', EnvironmentSettings::getServerName(), $content);
    	
    	$file_name = tempnam(sys_get_temp_dir(), 'intercom');
    	
    	file_put_contents($file_name, $content);
    	
    	$object->addScriptFile($file_name);
    }
}