<?php

 include('header.php');
 include('methods/c_access_methods.php');
 include('methods/c_date_methods.php');
 include('methods/c_participant_methods.php');
  
 if ( $_REQUEST['mode'] == 'rights' )
 {
    include('views/permissions/AccessRightPage.php');
     
    $page = new AccessRightPage;
 }
 elseif ( $_REQUEST['mode'] == 'spenttime' )
 {
    include('views/reports/ReportSpentTimePage.php');
 	$page = new ReportSpentTimePage;
 }
 else
 {
    include('views/project/ParticipantPage.php');
    $page = new ParticipantPage;
 }
 
 $page->render();
