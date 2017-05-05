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
    	$user_it = $this->session->getUserIt();
    	if ( $user_it->getId() < 1 ) return;
    	
        $object->addScriptFile(SERVER_ROOT_PATH."plugins/dobassist/resources/js/crisp.js");
        return;

        $dt = new DateTime($user_it->get('RecordCreated'));
        $content = file_get_contents(SERVER_ROOT_PATH."plugins/dobassist/resources/js/crisp.js");
    	$content = preg_replace('/%name%/', IteratorBase::wintoutf8($user_it->getDisplayName()), $content);
    	$content = preg_replace('/%email%/', hash_hmac("sha256", $user_it->get('Email'), "HyfFAbuV5E2LVTpiwsXnntH2eBL9OgNbqp2WACtz"), $content);
    	$content = preg_replace('/%date%/', $dt->getTimestamp(), $content);
    	$content = preg_replace('/%users%/', getFactory()->getObject('User')->getRegistry()->Count(), $content);
    	$content = preg_replace('/%host%/', EnvironmentSettings::getServerName(), $content);
    	
    	$project_it = getFactory()->getObject('Project')->getRegistry()->Query();
    	$content = preg_replace('/%projects%/', $project_it->count(), $content);
    	$content = preg_replace('/%user_role%/', $user_it->get('IsAdmin') == 'Y' ? 'admin' : 'regular', $content);
    	
    	$file_name = tempnam(sys_get_temp_dir(), 'intercom');
    	file_put_contents($file_name, $content);
    	$object->addScriptFile($file_name);
    }
}