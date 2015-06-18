<?php

	$cachedir = dirname(__FILE__) . '/cache';
	$cssdir   = dirname(__FILE__) . '/';
	$jsdir    = dirname(__FILE__) . '/scripts';

	if ( $_GET['type'] == '' ) $_GET['type'] = 'javascript';
	 
	switch ( $_GET['type'] )
	{
		case 'css':
			$_GET['files'] = 
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
				'styles/newlook/sidebar.css';
			break;
		
		case 'print':
			$_GET['type'] = 'css';
			$_GET['files'] = 
				'styles/legacy/style_print.css';
			break;
			
		default:
			switch( $_GET['asset'] )
			{
				case '1':
					$_GET['files'] = 
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
						'flot/jquery.flot.stack.min.js,'.
						'flot/jquery.flot.crosshair.min.js';
					break;
				case '2':
					$_GET['files'] = 
						'jquery/jquery.cookies.2.2.0.min.js,' .
						'modernizr/modernizr.js,' .
						'jquery/jquery.base64.min.js,'.
						'jquery/jquery.ba-resize.min.js,'.
						'jquery/imagesloaded.pkgd.min.js,'.
						'color-picker/jquery.colorPicker.min.js,'.
						'time/jstz-1.0.4.min.js';
					break;
				default:
					$_GET['files'] = 
						'pm/common.js,'.
		                'pm/board.js,'.
						'pm/document.js';
					break;
			}
	}
	
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