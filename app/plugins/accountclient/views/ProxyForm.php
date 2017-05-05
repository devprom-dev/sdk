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
		$html = file_get_contents(accountClientPlugin::SERVER_URL.'/module/account/form?'.$query);
		if ( $html == '' ) {
            echo str_replace(
                '%2', INSTALLATION_UID,
                    str_replace('%1', HELP_SUPPORT_URL, text(2450))
            );
        }
        else {
            echo $html;
        }
	}
}
