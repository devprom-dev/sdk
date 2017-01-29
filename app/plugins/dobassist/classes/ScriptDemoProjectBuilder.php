<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ScriptBuilder.php";

class ScriptDemoProjectBuilder extends ScriptBuilder
{
	private $session = null;
	
	function __construct( $session )
	{
		$this->session = $session;
	}
	
    public function build( ScriptRegistry & $object )
    {
        $language = getSession()->getUserIt()->get('Language') == 1 ? 'ru' : 'en';
        $object->addScriptFile(SERVER_ROOT_PATH."plugins/dobassist/resources/$language/locale.js");
    	$object->addScriptFile(SERVER_ROOT_PATH."plugins/dobassist/resources/productdemo.js");
    }
}