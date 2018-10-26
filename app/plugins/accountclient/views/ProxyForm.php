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

        $curl = CurlBuilder::getCurl();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, accountClientPlugin::SERVER_URL.'/module/account/form?'.$query);
        curl_setopt($curl, CURLOPT_HTTPGET, true);
        $html = curl_exec($curl);

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
