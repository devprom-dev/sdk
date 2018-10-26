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
 
 class CoRestoreRequest extends CommandForm
 {
 	function validate()
 	{
 		global $_REQUEST, $model_factory;
 		
		$this->checkRequired( array('Email') );
 		
		$this->user = $model_factory->getObject('cms_User');
		$this->user_it = $this->user->getByRef("TRIM(LCASE(Email))", trim(strtolower($_REQUEST['Email'])));

		if ( $this->user_it->count() < 1 )
		{
			$this->replyError(text('procloud220'));
		}
		
		return true;
 	}
 	
 	function create()
	{
 		global $model_factory;

		$settings = $model_factory->getObject('cms_SystemSettings');
 		$settings_it = $settings->getAll();

		// отправляем уведомление со ссылкой для сброса пароля
		$body = str_replace( '%1', $this->user_it->getResetPasswordKey(), text('procloud579'));
		
   		$mail = new HtmlMailbox;
   		$mail->appendAddress($this->user_it->get('Email'));
   		$mail->setBody($body);
   		$mail->setSubject( text('procloud222') );
   		$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
		$mail->send();
		
		// перенаправляем пользователя в проект
		$this->replySuccess(text('procloud580'));
	}
 }
 
?>