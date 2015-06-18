<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_teammanage.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 
 class CoSendMessage extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory, $user_it;

		$this->message = $model_factory->getObject('co_Message');

		// proceeds with validation
		$this->checkRequired( array('Subject', 'Content', 'Question', 'ToUser') );

		$this->checkWordsCount( 'Content', 4 );

		// check for answer
		$question = $model_factory->getObject('cms_CheckQuestion');
		
		$check_result = $question->checkAnswer( $_REQUEST['QuestionHash'],
			$this->Utf8ToWin($_REQUEST['Question']) );
		
		if ( !$check_result )
		{
			$this->replyError( 
				$this->getResultDescription( -14 ) );
		}

		// check authorization was successfull
		if ( !$user_it->IsReal() )
		{
			return false;
		}
		
		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST, $model_factory, $user_it;

		$parms = array( 
			'Subject' => $this->Utf8ToWin($_REQUEST['Subject']),
			'Content' => $this->Utf8ToWin($_REQUEST['Content']),
			'Author' => $user_it->getId() 
		);
		
		if ( $_REQUEST['ToUser'] > 0 )
		{ 
			$parms['ToUser'] = $_REQUEST['ToUser'];
		}
		
		$message_id = $this->message->add_parms( $parms );
	
		if ( $message_id < 1 )
		{
			$this->replyError( 
				$this->getResultDescription( 1001 ) );
		}

		$this->replySuccess( 
			$this->getResultDescription( 1000 ), $message_id );
	}

	function getResultDescription( $result )
	{
		switch($result)
		{
			case -14:
				return text('procloud216');

			case 3:
				return text('procloud512');

			case 1001:
				return text('procloud180');

			case 1000:
				return text('procloud181');

			default:
				return parent::getResultDescription( $result );
		}
	}
 }
 
?>