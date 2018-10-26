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
 
 class CoReportIssue extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory, $user_it, $project_it;

		$this->request = $model_factory->getObject('pm_ChangeRequest');

		// proceeds with validation
		$this->checkRequired( array('Caption', 'Type', 'Question') );

		$this->checkWordsCount( 'Caption', 4 );

		// check for answer
		$question = $model_factory->getObject('cms_CheckQuestion');
		
		$check_result = $question->checkAnswer( $_REQUEST['QuestionHash'],
			$this->Utf8ToWin($_REQUEST['Question']) );
		
		if ( !$check_result )
		{
			$this->replyError( 
				$this->getResultDescription( -14 ) );
		}

		if ( !is_object($project_it) )
		{
			return false;
		}
		
		if ( !$user_it->IsReal() )
		{
			$ask_url = '/requests/'.$project_it->get('CodeName').'/action/report';
			$this->replyError( text('procloud635').
				'<script type="text/javascript">$(document).ready(function(){getLoginForm("'.$ask_url.'");});</script>' );
		}
		
		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST, $model_factory, $project_it, $user_it;

		$result_id = $this->request->add_parms(
			array( 'Description' => $this->Utf8ToWin($_REQUEST['Caption']),
				   'Author' => $user_it->getId(),
				   'Caption' => $project_it->getWordsOnlyValue( $this->Utf8ToWin($_REQUEST['Caption']), 5 ),
				   'Project' => $project_it->getId(),
				   'Priority' => '3',
				   'Type' => $_REQUEST['Type'] )
			);
	
		if ( $result_id < 1 )
		{
			$this->replyError( 
				$this->getResultDescription( -1 ) );
		}

		$sub = $model_factory->getObject('co_ProjectSubscription');

		$it = $sub->getByRefArray( 
			array( 'Project' => $project_it->getId(), 
				   'SystemUser' => $user_it->getId() ) );

		if ( $it->count() < 1 )
		{
			$sub->add_parms( 
				array( 'Project' => $project_it->getId(), 
					   'SystemUser' => $user_it->getId() ) );
		}

		$this->replySuccess( 
			$this->getResultDescription( 1000 ) );
	}

	function getResultDescription( $result )
	{
		switch($result)
		{
			case -1:
				return text('procloud501');

			case -14:
				return text('procloud216');

			case 3:
				return text('procloud506');

			case 1000:
				return text('procloud507');

			default:
				return parent::getResultDescription( $result );
		}
	}
 }
 
?>