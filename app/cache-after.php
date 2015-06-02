<?php

	$cachedir = dirname(__FILE__) . '/cache';
	$jsdir    = dirname(__FILE__) . '/scripts';

	$_GET['type'] = 'javascript';
	
	switch ( $_GET['dpl'] )
	{
		case 'ru':
			$filesbylang .= 'jquery-ui/i18n/jquery.ui.datepicker-ru.js,datejs/date-ru-RU.js';
			break;
			
		case 'en-US':
			$filesbylang .= 'jquery-ui/i18n/jquery.ui.datepicker-en-US.js,datejs/date-en-US.js';
			break;
			
		case 'en-GB':
			$filesbylang .= 'jquery-ui/i18n/jquery.ui.datepicker-en-GB.js,datejs/date-en-GB.js';
			break;
	}
	
	$_GET['files'] = 
		'bootstrap/bootstrap.min.js,'.
		'bootstrap/bootstrap-filestyle-0.1.0.min.js,'.
		'bootstrap/bootstrap-contextmenu.js,'.
		'jquery-ui/jquery-ui-1.8.23.custom.min.js,'.
		'jquery-ui/jquery.ui.touch-punch.min.js,'.
		$filesbylang;
	
	if(!ob_start("ob_gzhandler")) ob_start();
	
	// Determine the directory and type we should use
	switch ($_GET['type']) {
		case 'css':
			$base = $cssdir;
			break;
		case 'javascript':
			$base = $jsdir;
			break;
	};

	$expires = 60 * 60 * 24 * 1;
	
 	header("Pragma: public");
 	header("Cache-Control: maxage=". $expires);
 	header("Expires: " . gmdate('D, d M Y H:i:s', time()+$expires) . " GMT");
	header("Content-Type: text/".$_GET['type']."; charset=utf-8");
	
	foreach( explode(',', $_GET['files']) as $element )
	{
		echo file_get_contents($base . '/' . $element);
		echo "\n\n";
		
		ob_flush();
	}

	ob_end_clean();