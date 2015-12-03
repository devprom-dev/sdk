<?php

class ProxyForm extends AjaxForm
{
	function getTemplate()
	{
		return '../../plugins/accountclient/views/templates/proxy.tpl.php';
	}
	
	function proxy()
	{
		$query = str_replace('module=', '', str_replace('namespace=', '', $_SERVER['QUERY_STRING']));
		echo file_get_contents(accountClientPlugin::SERVER_URL.'/module/account/form?'.$query);
	}
}
