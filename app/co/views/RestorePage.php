<?php

include('RestoreForm.php');

class ForgetPasswordPage extends CoPage
{
 	function getTable() {
		return new ForgetPasswordForm( getFactory()->getObject('cms_User') );
 	}

    function getFullPageRenderParms()
    {
        return array_merge(
            parent::getFullPageRenderParms(),
            array(
                'inside' => false
            )
        );
    }
}
