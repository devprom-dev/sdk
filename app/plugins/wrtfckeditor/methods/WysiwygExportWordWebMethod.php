<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class WysiwygExportWordWebMethod extends WebMethod
{
 	function getCaption()
 	{
 		return text('wrtfckeditor2');
 	}
 	
 	function getRedirectUrl()
 	{
 		$item = getFactory()->getObject('Module')->getExact('wrtfckeditor/exportmsword')->buildMenuItem();
 		
 		return $item['url'];
 	}
 	
 	function execute_request()
 	{
 	}
} 