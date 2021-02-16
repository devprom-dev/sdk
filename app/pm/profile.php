<?php

 include('header.php');
 include('views/project/ProfilePage.php');
 include('views/watchers/WatchingsPage.php');

 $part_it = getSession()->getParticipantIt();

 if ( !is_object($part_it) )
 {
 	exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
 }
 
 if ( $part_it->count() < 1 )
 {
 	exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
 }

 if ( $_REQUEST['mode'] == 'watchings' )
 {
 	$page = new WatchingsPage;
 }
 else
 {
 	$page = new ProfilePage;
 }
 
 $page->render();
