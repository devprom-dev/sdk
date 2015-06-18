<?php
/*
 * DEVPROM (http://www.devprom.net)
 * blog.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 include('common.php');
 
 $_REQUEST['mode'] = strtolower(trim($_REQUEST['mode']));
 
 switch ( $_REQUEST['mode'] )
 {
 	case 'map':

		$urls = array();
	
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/xml; charset=utf-8');

		echo '<?xml version="1.0" encoding="UTF-8"?>'.chr(10);
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.chr(10);
		
		foreach ( $urls as $location )
		{
			echo '<url>'.chr(10);
				echo '<loc>'.$project_it->wintoutf8($location).'</loc>'.chr(10);
				echo '<lastmod>'.date('Y-m-d').'</lastmod>'.chr(10);
				echo '<changefreq>daily</changefreq>'.chr(10);
			echo '</url>'.chr(10);
		}
		echo '</urlset>';

 		break;

 	case 'index':

		$urls = array();
		
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header('Content-type: text/xml; charset=utf-8');

		echo '<?xml version="1.0" encoding="UTF-8"?>'.chr(10);
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.chr(10);
		
		foreach ( $urls as $location )
		{
			echo '<sitemap>'.chr(10);
				echo '<loc>'.$project_it->wintoutf8($location).'</loc>'.chr(10);
				echo '<lastmod>'.date('Y-m-d').'</lastmod>'.chr(10);
			echo '</sitemap>'.chr(10);
		}
		echo '</sitemapindex>';
		
 		break;
 }
 
 ?>