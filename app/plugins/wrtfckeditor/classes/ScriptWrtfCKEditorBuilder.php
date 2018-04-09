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
    	$language = strtolower(getSession()->getLanguageUid());

        $object->addScriptFile(SERVER_ROOT_PATH."/plugins/wrtfckeditor/resources/js/locals/".$language."/resource.js");
        $object->addScriptText("
            $.extend(ckeditor_resources, {
                'new-issue': '".text('wrtfckeditor10')."',
                'new-task': '".translate('Создать задачу')."',
                'issue-title': '".translate('Пожелание')."',
                'task-title': '".translate('Задача')."'
            });
        ");

        $object->addScriptPath("/plugins/wrtfckeditor/ckeditor/ckeditor.js");
		$object->addScriptFile(SERVER_ROOT_PATH."/plugins/wrtfckeditor/ckeditor/global.js");
		$object->addScriptFile(SERVER_ROOT_PATH."/plugins/wrtfckeditor/resources/js/underi18n.js");
		$object->addScriptFile(SERVER_ROOT_PATH."/plugins/wrtfckeditor/ckeditor/plugins/codesnippet/lib/highlight/highlight.pack.js");
		$object->addScriptFile(SERVER_ROOT_PATH."/plugins/wrtfckeditor/ckeditor/wysiwyg.js");
		$object->addScriptFile(SERVER_ROOT_PATH."/scripts/jquery/jquery.textcomplete.js");
    }
}