<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/StyleSheetBuilder.php";

class StylesheetWrtfCKEditorBuilder extends StyleSheetBuilder
{
	private $session = null;
	
	function __construct( $session )
	{
		$this->session = $session;
	}
	
    public function build( StyleSheetRegistry & $object )
    {
    	$object->addScriptFile(SERVER_ROOT_PATH.'/plugins/wrtfckeditor/ckeditor/contents.css');
		
		$object->addScriptFile(SERVER_ROOT_PATH."/plugins/wrtfckeditor/ckeditor/inline-content.css");

    	$object->addScriptFile(SERVER_ROOT_PATH.'/styles/wysiwyg/msword.css');
		
    	$object->addScriptFile(SERVER_ROOT_PATH.'/styles/newlook/medium-fonts.css');
		
    	$object->addScriptFile(DOCUMENT_ROOT.'conf/plugins/wrtfckeditor/custom.css');
		
    	$object->addScriptFile(SERVER_ROOT_PATH."/plugins/wrtfckeditor/ckeditor/plugins/codesnippet/lib/highlight/styles/default.css");
    }
}