<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/StyleSheetBuilder.php";

class ProjectWelcomeStylesheetBuilder extends StyleSheetBuilder
{
	private $session = null;
	
	function __construct( $session )
	{
		$this->session = $session;
	}
	
    public function build( StyleSheetRegistry & $object )
    {
    	$object->addScriptFile(SERVER_ROOT_PATH.'/co/bundles/Devprom/ApplicationBundle/Resources/css/ProjectWelcomePage.css');
    }
}