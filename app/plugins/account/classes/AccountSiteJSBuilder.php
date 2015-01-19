<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ScriptBuilder.php";

class AccountSiteJSBuilder extends ScriptBuilder
{
	private $session = null;
	
	public function __construct( $session )
	{
		$this->session = $session;
	}
	
    public function build( ScriptRegistry & $object )
    {
 		$language = strtolower($this->session->getLanguage()->getLanguage());
    	$object->addScriptFile(SERVER_ROOT_PATH."plugins/account/resources/js/underi18n.js");
    	$object->addScriptFile(SERVER_ROOT_PATH."plugins/account/resources/js/".$language."/resource.js");
 		$object->addScriptFile(SERVER_ROOT_PATH."plugins/account/resources/js/account-form.js");
    }
}