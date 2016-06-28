<?php

$etagFile = md5(__FILE__)."-".$_REQUEST['v'];
$etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
if ($etagHeader == $etagFile) {
	exit(header("HTTP/1.1 304 Not Modified"));
}

	$cachedir = dirname(__FILE__) . '/cache';
	$jsdir    = dirname(__FILE__) . '/scripts';

	$type = 'javascript';
	
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
	
	$files =
		'bootstrap/bootstrap.min.js,'.
		'bootstrap/bootstrap-filestyle-0.1.0.min.js,'.
		'bootstrap/bootstrap-contextmenu.js,'.
		'jquery-ui/jquery-ui-1.8.23.custom.min.js,'.
		'jquery-ui/jquery.ui.touch-punch.min.js,'.
		$filesbylang;
	
	if(!ob_start("ob_gzhandler")) ob_start();
	
	// Determine the directory and type we should use
	switch ($type) {
		case 'css':
			$base = $cssdir;
			break;
		case 'javascript':
			$base = $jsdir;
			break;
	};

	header('Cache-Control: public');
	header("ETag: ". $etagFile);
	header("Last-Modified: Fri, 01 Apr 2012 12:33:50 GMT");
	header("Content-Type: text/".$type."; charset=utf-8");
	
	foreach( explode(',', $files) as $element )
	{
		echo file_get_contents($base . '/' . $element);
		echo "\n\n";
		
		ob_flush();
	}

	ob_end_clean();