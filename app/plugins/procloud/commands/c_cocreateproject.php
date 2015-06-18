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
 class CoCreateProject extends CreateProject
 {
 	function getStrategy()
 	{
 		return new ProjectActivationStrategy;
 	}

 	function validate()
 	{
 		global $model_factory, $_REQUEST;
 		
 		$_REQUEST['Language'] = '1';
 		$_REQUEST['Access'] = 'private';
 		
 		if ( $_REQUEST['Template'] != '' )
 		{
			$template = $model_factory->getObject('pm_ProjectTemplate');
			$template_it = $template->getExact( $_REQUEST['Template'] );
			
			if ( $template_it->count() > 0 )
			{
 				$_REQUEST['Template'] = $template_it->get('FileName');
			}
			else
			{
				$_REQUEST['Template'] = '';
			}
 		}
 		
 		if ( $_REQUEST['Template'] == '' )
 		{
 			$_REQUEST['Template'] = 'issuetr_ru.xml';
 		}
 		
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
		global $model_factory;
		
		$model_factory->enableVpd(false);
 		$model_factory->object_factory->access_policy = new AccessPolicy;

		return parent::create();
	}
 
 	function getResultDescription( $result )
	{
		if ( $result > 0 )
		{
			return text('procloud616');
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

 ////////////////////////////////////////////////////////////////////////////
 class ProjectActivationStrategy extends ProjectCreationStategy
 {
 	var $creation_it;
 	
 	function ProjectActivationStrategy ( $creation_it = null )
 	{
 		$this->creation_it = $creation_it;
 	}
 	
 	function execute()	
 	{
 		global $_REQUEST, $model_factory;

		if ( !is_null($this->creation_it) )
		{
	 		$this->user_id = $this->creation_it->get('SystemUser');
	 		$this->code_name = $this->creation_it->get('CodeName');
	 		$this->caption = $this->creation_it->get('Caption');
	 		$this->language = $this->creation_it->get('Language');
	 		$this->methodology = $this->creation_it->get('Methodology');
	 		$this->access = $this->creation_it->get('Access');
	 		
	 		$project_id = $this->createProject();
	 		
	 		if ( $project_id > 0 )
	 		{
	 			$this->creation_it->modify(
	 				array ( 'Project' => $project_id )
	 				);
	 		}
	 		
	 		return $project_id;
		}
		else
		{
			$prj_cr_cls = $model_factory->getObject('pm_ProjectCreation');
	
			$user_cls = $model_factory->getObject('cms_User');
			$user_it = $user_cls->getExact($_REQUEST['User']);
			
			if ( $user_it->count() < 1 ) 
			{
				return -1;
			}
			
			$parms = array();
			
			$parms['SystemUser'] = $user_it->getId();
			$parms['IPAddress'] = $_SERVER['REMOTE_ADDR'];
			$parms['Project'] = 0;
			$parms['CodeName'] = $_REQUEST['Codename'];
			$parms['Caption'] = $_REQUEST['Caption'];
			$parms['Language'] = $_REQUEST['Language'];
			$parms['Access'] = $_REQUEST['Access'];
			$parms['Methodology'] = $_REQUEST['Template'];
			
			$creation_id = $prj_cr_cls->add_parms($parms);
			
			if ( $creation_id < 1 )
			{
				return -4;
			}
	
			$creation_it = $prj_cr_cls->getExact($creation_id);
			
			$this->sendNotification( $creation_it );
			
			return $creation_id;
		}
 	}

	function getActivationKey( $creation_it )
	{
		return md5($creation_it->get('CodeName').
			$creation_it->get('SystemUser').INSTALLATION_UID.$creation_it->getId());
	}
	
	function sendNotification( $creation_it )
	{
 		global $model_factory;
 		
		$settings = $model_factory->getObject('cms_SystemSettings');
 		$settings_it = $settings->getAll();

 		$key = $this->getActivationKey( $creation_it );
 		
		$creation_it->modify( 
			array('CreationHash' => $key ) 
		);
 		
		$user_it = $creation_it->getRef('SystemUser');
		
		// отправляем уведомление со ссылкой для активации
		$greatings = translate('Здравствуйте, %s!').'<br/><br/>';
		$greatings = str_replace('%s', $user_it->getDisplayName(), $greatings);
		
		$body = $greatings.text('procloud64');

		$body = str_replace('%1', $creation_it->get('Caption'), $body);
		$body = str_replace('%2', _getServerUrl(), $body);
		$body = str_replace('%3', 
			_getServerUrl().'/room/activateproject?key='.$key, $body);
		
   		$mail = new HtmlMailbox;
   		$mail->appendAddress($user_it->get('Email'));
   		$mail->setBody($body);
   		$mail->setSubject( text('procloud228') );
   		$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
		$mail->send();
	}
	
 	function getSuccessMessage()
 	{
 		return text('procloud230');
 	}
 }

?>