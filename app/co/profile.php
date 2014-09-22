<?php

 include('header.php');
 include('views/ProfilePage.php');
 
 if ( $_REQUEST['user'] == '' )
 {
 	$_REQUEST['user'] = $user_it->getId();
 }
 
 if ( $user_it->getId() != $_REQUEST['user'] )
 {
 	exit(header('Location: /404?redirect='.urlencode($_SERVER['REQUEST_URI'])));
 }

 ////////////////////////////////////////////////////////////////////////////////
 $page = new ProfilePage( $_REQUEST['mode'] );

 $page->render();
 
?>