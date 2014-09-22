<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ScriptBuilder.php";

class ScriptWrtfCKEditorBuilder extends ScriptBuilder
{
	private $session = null;
	
	function __construct( $session )
	{
		$this->session = $session;
	}
	
    public function build( ScriptRegistry & $object )
    {
    	$object->addScriptPath("/plugins/wrtfckeditor/ckeditor/ckeditor.js");
    	
		$object->addScriptFile(SERVER_ROOT_PATH."/plugins/wrtfckeditor/ckeditor/global.js");

		$object->addScriptFile(SERVER_ROOT_PATH."/plugins/wrtfckeditor/ckeditor/wysiwyg.js");
    }
}