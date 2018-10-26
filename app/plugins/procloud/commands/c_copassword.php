<?php

class CoPassword extends CommandForm
{
 	function validate()
	{
		global $_REQUEST, $model_factory, $user_it;

		// check authorization was successfull
		if ( $user_it->getId() != $_REQUEST['object_id'] )
		{
			return false;
		}

		$this->user = $model_factory->getObject('cms_User');
		$this->user_it = $this->user->getExact( $_REQUEST['object_id'] );

		// proceeds with validation
		$this->checkRequired( 
			array('OldPassword', 'NewPassword', 'RepeatPassword') );

		$password_hash = $this->user->
			getHashedPassword( $_REQUEST['OldPassword'] );

		if( $password_hash != $this->user_it->get('Password') ) 
		{
			$this->replyError( $this->getResultDescription( -2 ) );
		}

		if( $_REQUEST['NewPassword'] != $_REQUEST['RepeatPassword'] ) 
		{
			$this->replyError( $this->getResultDescription( -3 ) );
		}
		
		return true;
	}

	function modify( $user_id )
	{
		global $_REQUEST, $model_factory;
		
		if ( is_object($this->user_it) )
		{
			$this->user_it->modify( 
				array('Password' => $_REQUEST['NewPassword']) );
				
			$session = getSession();
			$session->open( $this->user_it );
			
			$settings = $model_factory->getObject('cms_SystemSettings');
	 		$settings_it = $settings->getAll();
	
			// отправляем уведомление со ссылкой для сброса пароля
			$body = str_replace( '%1', $this->user_it->get('Login'), text('procloud584'));
			$body = str_replace( '%2', $_REQUEST['NewPassword'], $body);
			
	   		$mail = new HtmlMailbox;
	   		$mail->appendAddress($this->user_it->get('Email'));
	   		$mail->setBody($body);
	   		$mail->setSubject( text('procloud585') );
	   		$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
			$mail->send();
			
			$this->replySuccess( $this->getResultDescription( -1 ) );
		}
		else
		{
			$this->replyError( $this->getResultDescription( -4 ) );
		}
	}
	
	function getResultDescription( $result )
	{
		switch($result)
		{
			case -1:
				return text('procloud546');

			case -2:
				return text('procloud545');
				
			case -3:
				return text('procloud232');

			case -4:
				return text('procloud233');

			default:
				return parent::getResultDescription( $result );
		}
	}
 }
