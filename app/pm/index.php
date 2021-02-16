<?php

 include('header.php');

 $mode = $_REQUEST['mode'];
 
 $blog_id = $_REQUEST['blog_id'];
 
 if( $mode == '' || $mode == 'log' )
 {
 	include('views/communications/ProjectLogPage.php');

 	$page = new ProjectLogPage;

	$page->render();
 }
 elseif( $mode == 'scrum' ) 
 {
 	include('views/c_scrum_view.php');
 	
 	$page = new ScrumPage;

	$page->render();
 }
 elseif( $mode == 'methodology' )
 {
 	include('views/settings/MethodologyPage.php');
 	
 	$page = new MethodologyPage;

	$page->render();
 }
 elseif( in_array($mode, array('settings', 'import-settings', 'export-settings')) )
 {
 	include('views/settings/ProjectSettingsPage.php');
 	
 	$page = new ProjectSettingsPage;

	$page->render();
 }
 elseif ( $mode == 'question')
 {
	include('views/communications/QuestionPage.php');
	 
 	$page = new QuestionPage;

	$page->render();
 }
 elseif ( $mode == 'tags')
 {
 	include('views/tags/TagPage.php');

 	$page = new TagPage;

	$page->render();
 }
 elseif( $mode == 'dicts' || $mode == 'workflow' )
 {
 	include('views/settings/DictionaryPage.php');
 	
 	$page = new DictionaryPage;

	$page->render();
 }
 elseif( $mode == 'reports' )
 {
 	include('views/reports/ReportPage.php');
 	
 	$page = new ReportPage;

	$page->render();
 }
 else 
 {
 }
