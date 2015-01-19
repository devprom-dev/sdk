<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/StyleSheetBuilder.php";

class AccountSiteCssBuilder extends StyleSheetBuilder
{
    public function build( StyleSheetRegistry & $object )
    {
    	$object->addScriptFile(SERVER_ROOT_PATH.'/plugins/account/resources/css/account-form.css');
    }
}