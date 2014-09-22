<?php

 include('header.php');
 
 if( $_REQUEST['mode'] == 'milestone' )
 {
     include('views/project/MilestonePage.php');
     
     $page = new MilestonePage;
 	
	 $page->render();
 }
 else
 {
     include('views/project/VersionPage.php');
         
     $version_page = new VersionPage;
    
	$version_page->render();
 }
