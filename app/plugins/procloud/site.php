<?php
/*
 * DEVPROM (http://www.devprom.net)
 * state.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 include('site_core.php');

 $mode = $_REQUEST['project'];

 if ( $project_it->count() < 1 || !$project_it->HasProductSite() )
 {
 	exit(header('Location: /'));
 }

 switch ( $mode )
 {
 	case 'devprom':
		include('sites/devprom/site.php');
		$page = new SiteDEVPROMPage;
 		break;
 		
 	default:
 		$page = new SiteProjectPage;
 }

 $page->draw();

?>