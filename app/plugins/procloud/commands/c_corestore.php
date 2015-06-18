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
 
 class CoRestore extends CommandForm
 {
 	function validate()
 	{
 		global $_REQUEST, $model_factory;
 		
		$this->checkRequired( array('Key', 'Password', 'Password2') );
 		
 		if ( $_REQUEST['Password'] != $_REQUEST['Password2'] )
 		{
			$this->replyError(text('procloud583'));
 		}
 		
		$this->user = $model_factory->getObject('cms_User');
		$this->user_it = $this->user->getAll();

		while ( !$this->user_it->end() )
		{
			if ( $this->user_it->getResetPasswordKey() == $_REQUEST['Key'] )
			{
				return true;
			}
			
			$this->user_it->moveNext();
		}
		
		$this->replyError(text('procloud581'));
 	}
 	
 	function create()
	{
 		global $model_factory, $_REQUEST;

		if ( $this->user_it->getId() > 0 )
		{
			$this->user_it->modify( array('Password' => $_REQUEST['Password']) );
			
			$settings = $model_factory->getObject('cms_SystemSettings');
	 		$settings_it = $settings->getAll();
	
			// отправляем уведомление со ссылкой для сброса пароля
			$body = str_replace( '%1', $this->user_it->get('Login'), text('procloud584'));
			$body = str_replace( '%2', $_REQUEST['Password'], $body);
			
	   		$mail = new HtmlMailbox;
	   		$mail->appendAddress($this->user_it->get('Email'));
	   		$mail->setBody($body);
	   		$mail->setSubject( text('procloud585') );
	   		$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
			$mail->send();

			$session = getSession();
			$session->open( $this->user_it );
		}
		
		// перенаправляем пользователя в проект
		$this->replyredirect('/',text('procloud582'));
	}
 }
 
?>