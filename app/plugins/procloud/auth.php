<?php
/*
 * DEVPROM (http://www.devprom.net)
 * auth.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 
 include ('common.php');
  
 header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
 header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
 header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
 header("Pragma: no-cache"); // HTTP/1.0
 header('Content-type: text/html; charset=windows-1251');

 switch ( $_REQUEST['mode'] )
 {
 	case 'question':
		$question = $model_factory->getObject('cms_CheckQuestion');
		$question_it = $question->getRandom();
		
		echo $_REQUEST['callback'].'(';
		 	echo '{"caption":"'.$question_it->getDisplayName().'",'.
		 		'"hash":"'.$question_it->getHash().'"}';
		echo ')';
		
		break;
		
	case 'key':
		if ( !is_object($project_it) )
		{
			echo $_REQUEST['callback'].'(';
			 	echo '{"error":"project name is required"}';
			echo ')';
			
			die();
		}

		if ( !is_object($user_it) )
		{
			echo $_REQUEST['callback'].'(';
			 	echo '{"error":"user is required"}';
			echo ')';
			
			die();
		}
		
		if ( $user_it->getId() != '' && $project_it->IsUserParticipate( $user_it->getId() ) )
		{
			echo $project_it->getFeedbackAuthKey();
		}
		
		break;
 }
 
?>
 