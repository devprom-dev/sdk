<?php

include "FailedForm.php";

class FailedController extends Page
{
 	function needDisplayForm() {
 		return true;
 	}

    function authorizationRequired() {
        return false;
    }

 	function getForm() {
 		return new FailedForm(getFactory()->getObject('License'));
 	}

    function render($view = null)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');

        return parent::render($view);
    }
}
