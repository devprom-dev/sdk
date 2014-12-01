<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ScriptBuilder.php";

class ProductTourJSBuilder extends ScriptBuilder
{
	private $session = null;
	
	public function __construct( $session )
	{
		$this->session = $session;
	}
	
    public function build( ScriptRegistry & $object )
    {
 		$language = strtolower($this->session->getLanguage()->getLanguage());
    	    	
    	$object->addScriptFile(SERVER_ROOT_PATH."plugins/producttour/resources/js/underi18n.js");
    	$object->addScriptFile(SERVER_ROOT_PATH."plugins/producttour/resources/js/bootstrap-tour.min.js");
    	$object->addScriptFile(SERVER_ROOT_PATH."plugins/producttour/resources/js/locals/".$language."/resource.js");
    	$object->addScriptFile(SERVER_ROOT_PATH."plugins/producttour/resources/js/locals.js");
    	
    	if ( getFactory()->getObject('UserSettings')->getSettingsValue('skip-product-tour') != 'true' )
    	{
    		$object->addScriptFile(SERVER_ROOT_PATH."plugins/producttour/resources/js/devprom-tour.js");
    	}
    	$object->addScriptFile(SERVER_ROOT_PATH."plugins/producttour/resources/js/setupformfields-tour.js");
    }
}