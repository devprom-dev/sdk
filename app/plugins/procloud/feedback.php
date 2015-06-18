<?php
/*
 * DEVPROM (http://www.devprom.net)
 * feedback.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 
 $path = dirname(dirname(__FILE__));
 
 include ('common.php');
 
 if ( $_REQUEST['project'] == '' )
 {
 	$codename = 'devprom';
 }
 else
 {
 	$codename = $_REQUEST['project'];
 }
 
 $project = $model_factory->getObject('pm_Project');
 $project_it = $project->getByRef('CodeName', $codename);

 header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
 header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
 header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
 header("Pragma: no-cache"); // HTTP/1.0
 header('Content-type: text/javascript; charset=windows-1251');

 include ($path.'/../scripts/jquery/jquery.form.js');
 include ($path.'/../scripts/feedback/feedback.js');
 
?>

$(document).ready(function() 
{
	feedbackOpts.authorEmail = '<? echo $user_it->get('Email') ?>';
	addFeedback('<? echo $codename?>', '<? echo $project_it->getDisplayName() ?>', 
		'<? echo _getServerUrl() ?>');
});

