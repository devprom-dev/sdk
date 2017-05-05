<?php

include_once "ContextResourceBuilder.php";

class ContextResourceFileBuilder extends ContextResourceBuilder
{
	private $session = null;
	
	public function __construct($session)
	{
		$this->session = $session;
	}
	
    function build( ContextResourceRegistry $object )
    {
    	$language = strtolower($this->session->getLanguageUid());

    	$strings = include(SERVER_ROOT_PATH."co/bundles/Devprom/CommonBundle/Resources/text/".$language."/context-resource.php");
    	
    	$module_object = getFactory()->getObject('Module');
    	
    	foreach( $strings as $module => $text )
    	{
    		$text = preg_replace_callback(
						'/\%module:([^\%]+)\%/i',
						function( $matches ) use ($module_object) {
								$module_it = $module_object->getExact($matches[1]);
								return '<a target="_blank" href="'.$module_it->get('Url').'">'.$module_it->getDisplayName().'</a>';
						},
						IteratorBase::utf8towin($text)
				);	

    		$text = preg_replace('/\%host\%/i', EnvironmentSettings::getServerUrl(),$text);
			$text = preg_replace('/\%schema\%/i', EnvironmentSettings::getServerSchema(),$text);
			$text = preg_replace('/\%servername\%/i', EnvironmentSettings::getServerName(),$text);
            $text = preg_replace('/\%server-url\%/i', EnvironmentSettings::getServerUrl(),$text);

    		$object->addText($module, $text);
    	}
    }
}