<?php

include_once "WebMethod.php";

class TranslateWebMethod extends WebMethod
{
 	function execute_request()
 	{
 		 global $_REQUEST, $model_factory;

		 echo $_REQUEST['callback'].'{"text":"'.text($_REQUEST['text']).'"}';
 	}
}
