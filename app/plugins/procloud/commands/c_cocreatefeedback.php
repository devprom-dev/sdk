<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_advisemanage.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 class CoCreateFeedback extends CreateProject
 {
 	function validate()
 	{
 		global $model_factory, $_REQUEST;
 		
 		$_REQUEST['Language'] = '1';
 		$_REQUEST['Access'] = 'private';
 		$_REQUEST['Template'] = 'issuetr_ru.xml';
 		
		// check for answer
		$question = $model_factory->getObject('cms_CheckQuestion');
		
		$check_result = $question->checkAnswer( $_REQUEST['QuestionHash'],
			$this->Utf8ToWin($_REQUEST['Question']) );
		
		if ( !$check_result )
		{
			$this->replyError( 
				$this->getResultDescription( -14 ) );
		}
 		
 		return parent::validate();
 	}
 	
 	function create()
	{
		return parent::create();
	}
 
 	function getResultDescription( $result )
	{
		if ( $result > 0 )
		{
			return text('procloud577');
		}
		
		switch($result)
		{
			case -14:
				return text('procloud216');
				
			default:
				return parent::getResultDescription( $result );
		}
	}
 }
 
?>