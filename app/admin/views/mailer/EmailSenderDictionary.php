<?php

class EmailSenderDictionary extends FieldDictionary
{
	function __construct()
	{
		parent::__construct( getFactory()->getObject('cms_SystemSettings') );
	}

	function getOptions()
	{
		$options = array();

	    $options[] = array (
               'value' => 'user',
               'caption' => text(1225),
               'disabled' => false
        );

	    $options[] = array (
               'value' => 'admin',
               'caption' => text(1226),
               'disabled' => false
        );
	    
	    return $options;
	}
}
