<?php

$etagFile = md5(__FILE__)."-".$_REQUEST['v'];
$etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);
if ($etagHeader == $etagFile) {
	exit(header("HTTP/1.1 304 Not Modified"));
}

	$cachedir = dirname(__FILE__) . '/cache';
	$cssdir   = dirname(__FILE__) . '/';
	$jsdir    = dirname(__FILE__) . '/scripts';

	// Determine the directory and type we should use
	switch ($_GET['type']) {
		case 'css':
			$base = $cssdir;
			$type = 'css';
			break;
		case 'print':
			$base = $cssdir;
			$type = 'print';
			break;
		case 'javascript':
		default:
			$base = $jsdir;
			$type = 'javascript';
			break;
	};

	$language = $_GET['l'];
	if ( !in_array($language, array('ru','en')) ) $language = 'en';

	switch ( $type )
	{
		case 'css':
			$files =
				'styles/jquery-ui/jquery-ui-1.8.16.custom.css,' .
				'styles/bootstrap/css/bootstrap.css,'.
				'styles/bootstrap/css/bootstrap-responsive.min.css,'.
				'styles/select/jquery_select.css,' .
				'styles/fancybox/fancy.css,'. 
				'styles/newlook/main.css,'.
				'styles/newlook/extended.css,'.
				'styles/newlook/medium-fonts.css,'.
				'styles/jquery/jquery.treeview.css,' .
				'scripts/color-picker/colorPicker.css,'.
				'styles/newlook/board.css,'.
				'styles/wysiwyg/codes.css,'.
				'styles/newlook/sidebar.css';
			break;
		
		case 'print':
			$type = 'css';
			$files =
				'styles/legacy/style_print.css';
			break;
			
		default:
			switch( $_GET['asset'] )
			{
				case '1':
					$files =
						'jquery/jquery-1.11.3.min.js,'.
						'jquery/jquery-migrate-1.2.1.js,'.
						'jquery/jquery.form.js,'.
						'jquery/jquery.treeview.js,'.
						'jquery/jquery.treeview.edit.js,'.
						'jquery/jquery.treeview.async.js,'.
						'fancybox/jquery.fancybox-1.0.0.js,'.
						'excanvas/excanvas.compiled.js,'.
						'flot/jquery.flot.min.js,'.
						'flot/jquery.flot.pie.min.js,'.
						'flot/jquery.flot.time.min.js,'.
						'flot/jquery.flot.stack.min.js,'.
						'flot/jquery.flot.crosshair.min.js,'.
						'flot/jquery.flot.threshold.min.js';
					break;
				case '2':
					$files =
						'jquery/jquery.cookies.2.2.0.min.js,' .
						'modernizr/modernizr.js,' .
						'jquery/jquery.base64.min.js,'.
						'jquery/jquery.ba-resize.min.js,'.
						'jquery/imagesloaded.pkgd.min.js,'.
						'color-picker/jquery.colorPicker.min.js,'.
						'locale/underi18n.js,' .
						'time/jstz-1.0.4.min.js';
					break;
				default:
					$files =
						'pm/locale/'.$language.'/resources.js,'.
						'pm/common.js,'.
		                'pm/board.js,'.
						'pm/document.js';
					break;
			}
	}
	
	header('Cache-Control: public');
	header("ETag: ". $etagFile);
	header("Last-Modified: Fri, 01 Apr 2012 12:33:50 GMT");
	header("Content-Type: text/".$type."; charset=utf-8");

	if(!ob_start("ob_gzhandler")) ob_start();
	foreach( explode(',', $files) as $element ) {
		echo file_get_contents($base . '/' . $element);
		echo "\n\n";
		ob_flush();
	}
	ob_end_clean();