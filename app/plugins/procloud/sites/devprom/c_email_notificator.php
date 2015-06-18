<?php

include_once SERVER_ROOT_PATH."cms/classes/EmailNotificator.php";

 ///////////////////////////////////////////////////////////////////////////////
 class DevpromEmailNotificatorHandler
 {
 	function getEmailAddress( $part_it ) 
 	{
		return $part_it->get('Email');
	}

 	function getSender( $object_it, $action ) 
 	{
		return 'Devprom <marketing@devprom.ru>';
 	}
 	
	function getSubject( $subject, $object_it, $prev_object_it, $action )
	{
		return $subject;
	}
	
	function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		return array();
	}	

	function getBody( $action, $object_it, $prev_object_it ) 
	{
		return '';
	}	

	function getMailBox() 
	{
		return new HtmlMailBox;
	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////
 class DevpromUserHandler extends DevpromEmailNotificatorHandler
 {
	function getSubject( $subject, $object_it, $prev_object_it, $action )
	{
		if ( $action != 'add' ) return '';
		
		return 'Регистрация на сайте devprom.ru';
	}
	
	function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		if ( $action != 'add' ) return array();
		
		return array($object_it->get('Email'));
	}	

	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		global $_REQUEST;

		if ( $action != 'add' ) return '';
		
		$body = 'Добрый день, %3!<br/><br/>Спасибо за ваш интерес к системе управления проектами <a href="http://devprom.ru">Devprom</a>!<br/><br/>Ваш логин и пароль для загрузки файлов и обновлений:<br/><b>%1</b><br/><b>%2</b><br/><br/>При необходимости, Вы можете <a href="%4">изменить</a> автоматически сформированный пароль.<br/><br/>Если у вас возникнут какие-либо проблемы с установкой, пожалуйста, попробуйте следующие шаги:<br/><br/>1. Загляните в <a href="http://devprom.ru/docs/%D0%A0%D1%83%D0%BA%D0%BE%D0%B2%D0%BE%D0%B4%D1%81%D1%82%D0%B2%D0%BE-%D0%B0%D0%B4%D0%BC%D0%B8%D0%BD%D0%B8%D1%81%D1%82%D1%80%D0%B0%D1%82%D0%BE%D1%80%D0%B0">руководство администратора</a>, в нем вы найдете решения по большинству нестандартных ситуаций;<br/><br/>2. задайте свой вопрос службе поддержки: <a href="http://support.devprom.ru">http://support.devprom.ru</a>.<br/><br/>Успешного вам завершения проектов!<br/><br/>--<br/>Команда Devprom<br/>http://devprom.ru';
		
		$body = str_replace('%3', $object_it->getDisplayName(), $body);
		$body = str_replace('%1', $object_it->get('Email'), $body);
		$body = str_replace('%2', $_REQUEST['PasswordOriginal'], $body);

		$body = str_replace('%4', 
			'http://devprom.ru/download?key='.$object_it->getResetPasswordKey(), $body);
		
		return $body;
	}	
 }
 
 ///////////////////////////////////////////////////////////////////////////////
 class DevpromEmailNotificator extends EmailNotificator
 {
 	var $handlers, $common_handler;
 	
	function DevpromEmailNotificator() 
	{
		parent::__construct();
		
		$this->common_handler = new DevpromEmailNotificatorHandler;
		$this->handlers = array( 'cms_User' => new DevpromUserHandler );
	}
 	
 	function getHandler( $object_it ) 
 	{
 		$handler = $this->handlers[$object_it->object->getClassName()];
		return is_object($handler) ? $handler : $this->common_handler;
 	}
 	
	function process( $action, $object_it, $prev_object_it ) 
	{
		if ( !is_object($object_it->object->entity) ) return;

		switch ( $object_it->object->entity->get('ReferenceName') )
		{
			case 'cms_User' :
				parent::process( $action, $object_it, $prev_object_it );
				break;
		}
	}

	function getSender( $object_it, $action ) 
	{
		$handler = $this->getHandler( $object_it );
		return $handler->getSender( $object_it, $action );
	}

	function getSubject( $object_it, $prev_object_it, $action, $recipient )
	{
		$handler = $this->getHandler( $object_it );
		return $handler->getSubject( parent::getSubject($object_it, $prev_object_it, $action, $recipient), 
			$object_it, $prev_object_it, $action );
	}

	function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		global $model_factory;
		
		$handler = $this->getHandler( $object_it );
		return $handler->getRecipientArray( $object_it, $prev_object_it, $action );
	}
	
	function getBody( $action, $object_it, $prev_object_it )
	{
		$handler = $this->getHandler( $object_it );
		return $handler->getBody( $action, $object_it, $prev_object_it );
	}	

	function getMailBox($object_it) 
	{
		$handler = $this->getHandler( $object_it );
		return $handler->getMailBox();
	}
 }
