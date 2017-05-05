<?php

include "AutoActionOperatorsDictionary.php";
include "AutoActionAttributesDictionary.php";

class AutoActionConditionsField extends Field
{
	private $object = null;
	
	function __construct($object)
	{
		$this->object = $object;
		parent::__construct();
	}
	
    function render( $view )
    {
        echo $view->render(SERVER_ROOT_PATH . 'pm/views/workflow/templates/AutoActionConditionsField.tpl.php',
        		array (
        				'field_attributes' => new AutoActionAttributesDictionary($this->object),
		        		'field_operators' => new AutoActionOperatorsDictionary(),
		        		'conditions' => JsonWrapper::decode(html_entity_decode($this->getValue(), ENT_QUOTES | ENT_HTML401, APP_ENCODING ))
        		)
        	);
    }
}