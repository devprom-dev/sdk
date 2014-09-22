<?php

	$cache 	  = true;
	$cachedir = dirname(__FILE__) . '/cache';
	$cssdir   = dirname(__FILE__) . '/';
	$jsdir    = dirname(__FILE__) . '/scripts';

	if ( $_GET['type'] == '' )
	{
		$_GET['type'] = 'javascript';
	}
	 
	switch ( $_GET['type'] )
	{
		case 'css':
			$_GET['files'] = 
				'styles/legacy/style.css,' .
				'styles/jquery-ui/jquery-ui-1.8.16.custom.css,' .
				'styles/jquery/jquery.treeview.css,' .
				'styles/select/jquery_select.css,' .
				'styles/fancybox/fancy.css,'.
				'styles/bootstrap/css/bootstrap-button.css,'.
				'styles/bootstrap/css/bootstrap-alerts.css,'.
				'styles/bootstrap/css/bootstrap-pagination.css'.
				'';

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
				'styles/newlook/sidebar.css'.
				'';
			break;
		
		case 'print':
			$_GET['type'] = 'css';
			$_GET['files'] = 
				'styles/legacy/style_print.css';
			break;
			
		default:
		    
			$_GET['files'] = 
				'jquery/jquery.min.js,'.
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
				'flot/jquery.flot.crosshair.min.js,'.
				'jquery/jquery.cookies.2.2.0.min.js,' .
				'modernizr/modernizr.js,' .
				'jquery/jquery.base64.min.js,'.
				'jquery/jquery.ba-resize.min.js,'.
				'jquery/imagesloaded.pkgd.min.js,'.
				'color-picker/jquery.colorPicker.min.js,'.
				'time/jstz-1.0.4.min.js,'.
				'pm/common.js,' .
                'pm/board.js,'.
				'pm/document.js';
	}
	
	$expires = 60 * 60 * 24 * 1;
	
 	header("Pragma: public");
 	header("Cache-Control: maxage=". $expires);
 	header("Expires: " . gmdate('D, d M Y H:i:s', time()+$expires) . " GMT");

	// Determine the directory and type we should use
	switch ($_GET['type']) {
		case 'css':
			$base = realpath($cssdir);
			break;
		case 'javascript':
			$base = realpath($jsdir);
			break;
		default:
			header ("HTTP/1.0 503 Not Implemented");
			exit;
	};

	$elements = explode(',', $_GET['files']);

	// Determine last modification date of the files
	$lastmodified = 0;
	while (list(,$element) = each($elements)) {
		$path = realpath($base . '/' . $element);

		if (($_GET['type'] == 'javascript' && substr($path, -3) != '.js') || 
			($_GET['type'] == 'css' && substr($path, -4) != '.css')) {
			header ("HTTP/1.0 403 Forbidden");
			exit;	
		}
	
		if (substr($path, 0, strlen($base)) != $base || !file_exists($path)) {
			header ("HTTP/1.0 404 Not Found");
			exit;
		}
		
		$lastmodified = max($lastmodified, filemtime($path));
	}

	// Send Etag hash
	$hash = $lastmodified . '-' . md5($_GET['files']);
	header ("Etag: \"" . $hash . "\"");
	
	if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && 
		stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $hash . '"') 
	{
		// Return visit and no modifications, so do not send anything
		header ("HTTP/1.0 304 Not Modified");
		header ('Content-Length: 0');
	} 
	else 
	{
		// First time visit or files were modified
		if ($cache) 
		{
			// Determine supported compression method
			$gzip = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');
			$deflate = strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate');
	
			// Determine used compression method
			$encoding = $gzip ? 'gzip' : ($deflate ? 'deflate' : 'none');
	
			// Check for buggy versions of Internet Explorer
			if (!strstr($_SERVER['HTTP_USER_AGENT'], 'Opera') && 
				preg_match('/^Mozilla\/4\.0 \(compatible; MSIE ([0-9]\.[0-9])/i', $_SERVER['HTTP_USER_AGENT'], $matches)) {
				$version = floatval($matches[1]);
				
				if ($version < 6)
					$encoding = 'none';
					
				if ($version == 6 && !strstr($_SERVER['HTTP_USER_AGENT'], 'EV1')) 
					$encoding = 'none';
			}
			
			// Try the cache first to see if the combined files were already generated
			$cachefile = 'cache-' . $hash . '.' . $_GET['type'] . ($encoding != 'none' ? '.' . $encoding : '');
						
			if (file_exists($cachedir . '/' . $cachefile)) {
				if ($fp = fopen($cachedir . '/' . $cachefile, 'rb')) {

					if ($encoding != 'none') {
						header ("Content-Encoding: " . $encoding);
					}
				
					header ("Content-Type: text/".$_GET['type']."; charset=utf-8");
					header ("Content-Length: " . filesize($cachedir . '/' . $cachefile));
		
					fpassthru($fp);
					fclose($fp);
					exit;
				}
			}
		}
	
		// Get contents of the files
		$contents = chr(hexdec('ef')).chr(hexdec('bb')).chr(hexdec('bf'));
		
		reset($elements);
		while (list(,$element) = each($elements)) {
			$path = realpath($base . '/' . $element);
			$contents .= "\n\n" . file_get_contents($path);
		}
	
		// Send Content-Type
		header ("Content-Type: text/".$_GET['type']."; charset=utf-8");
		
		if (isset($encoding) && $encoding != 'none') 
		{
			// Send compressed contents
			$contents = gzencode($contents, 9, $gzip ? FORCE_GZIP : FORCE_DEFLATE);
			header ("Content-Encoding: " . $encoding);
			header ('Content-Length: ' . strlen($contents));
			echo $contents;
		} 
		else 
		{
			// Send regular contents
			header ('Content-Length: ' . strlen($contents));
			echo $contents;
		}

		// Store cache
		if ($cache) 
		{
			if ($fp = fopen($cachedir . '/' . $cachefile, 'wb')) 
			{
				fwrite($fp, $contents);
				fclose($fp);
			}
		}
	}	
?>