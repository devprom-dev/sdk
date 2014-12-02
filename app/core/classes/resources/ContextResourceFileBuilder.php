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
    	$language = strtolower($this->session->getLanguage()->getLanguage());

    	$strings = include(SERVER_ROOT_PATH."co/bundles/Devprom/CommonBundle/Resources/text/".$language."/context-resource.php");
    	
    	foreach( $strings as $module => $text ) $object->addText($module, IteratorBase::utf8towin($text));
    }
}