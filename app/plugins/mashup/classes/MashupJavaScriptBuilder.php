<?php
include_once SERVER_ROOT_PATH."core/classes/widgets/ScriptBuilder.php";

class MashupJavaScriptBuilder extends ScriptBuilder
{
    public function build( ScriptRegistry & $object )
    {
        $object->addScriptFile(SERVER_ROOT_PATH."plugins/mashup/resources/js/mashup.js");
    }
}