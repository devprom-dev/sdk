<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ScriptBuilder.php";

class MaintenanceJSBuilder extends ScriptBuilder
{
	private $session = null;
	
	public function __construct( $session )
	{
		$this->session = $session;
	}
	
    public function build( ScriptRegistry & $object )
    {
 		$language = strtolower($this->session->getLanguageUid());
 		$content = file_get_contents(SERVER_ROOT_PATH."admin/bundles/Devprom/AdministrativeBundle/Resources/public/js/maintenance.js");
    	$content = preg_replace('/%iid%/', INSTALLATION_UID, $content);
 		$file_name = tempnam(sys_get_temp_dir(), 'maintenance');
    	file_put_contents($file_name, $content);
    	$object->addScriptFile($file_name);
    }
}