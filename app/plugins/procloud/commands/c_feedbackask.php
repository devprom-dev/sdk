<?php

 require_once (dirname(__FILE__).'/c_feedback_base.php');

 ////////////////////////////////////////////////////////////////////////////
 class FeedbackAsk extends Command
 {
 	function getProxy()
 	{
 		return new FeedbackBase;
 	}
 	
 	function execute()
	{
		global $_REQUEST, $model_factory, $project_it, 
			   $_FILES, $controller;

		// process common settings and verifications
		$proxy = $this->getProxy();
		$proxy->execute();
		
		$caption = $_REQUEST['caption'];
		
		if ( $caption == '' )
		{
		 	die();
		}
		
		if ( mb_detect_encoding($caption, 'UTF-8', true) == 'UTF-8' )
		{
			$caption = $project_it->utf8towin($caption);
		}
		 
		$question = $model_factory->getObject('pm_Question');
		
		$question_id = $question->add_parms(
		 	array (
		 		'Content' => $caption,
		 		'Author' => $user_it->getId()
		 		)
		 	);
		 	
		$question_it = $question->getExact($question_id);
		
		echo $_REQUEST['callback'].'(';
		 	echo '{"question":"'.$controller->getGlobalUrl($question_it).'"}';
		echo ')';
		 
		return $question_it;
	}
 }
 
?>