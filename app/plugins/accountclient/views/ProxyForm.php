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
		echo $this->wintoutf8(file_get_contents(accountClientPlugin::SERVER_URL.'/module/account/form?'.$query));
	}

	function wintoutf8($s)
 	{
 		if ( function_exists('mb_convert_encoding') ) return mb_convert_encoding($s, "utf-8", "cp1251");
 		if ( function_exists('iconv') ) return iconv("cp1251", "utf-8//IGNORE", $s);
		  $t = '';
		  for ($i = 0, $m = strlen($s); $i < $m; $i++) {
		    $c = ord($s[$i]);
		    if ($c <= 127) { $t .= chr($c); continue; }
		    if ($c >= 192 && $c <= 207) { $t .= chr(208) . chr($c - 48); continue; }
		    if ($c >= 208 && $c <= 239) { $t .= chr(208) . chr($c - 48); continue; }
		    if ($c >= 240 && $c <= 255) { $t .= chr(209) . chr($c - 112); continue; }
		    if ($c == 184) { $t .= chr(209) . chr(209); continue; };
		    if ($c == 168) { $t .= chr(208) . chr(129); continue; };
		  }
		  return $t;
 	}   
}
