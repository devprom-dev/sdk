<?php

include "ProxyForm.php";

class AccountProxyController extends Page
{
 	function needDisplayForm() {
 		return true;
 	}

    function authorizationRequired() {
        return getFactory()->getObject('User')->getRegistry()->Count() > 0;
    }

    function getForm() {
 		return new ProxyForm(getFactory()->getObject('License'));
 	}

 	function render($view = null)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');

        return parent::render($view);
    }
}
