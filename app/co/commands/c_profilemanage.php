<?php

 class ProfileManage extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory;

		// check authorization was successfull
		if ( getSession()->getUserIt()->getId() != $_REQUEST['object_id'] )
		{
			return false;
		}

		$this->user = $model_factory->getObject('cms_User');

		// proceeds with validation
		$this->checkRequired( 
			array('Caption', 'Email', 'Login') );
		
		return true;
 	}
 	
	function modify( $user_id )
	{
		global $_REQUEST, $_FILES, $model_factory;

		$this->user_it = $this->user->getExact($user_id);
		
		$this->checkUniqueExcept( $this->user_it, 
			$this->Utf8ToWin('Email') );
			
		$this->checkUniqueExcept( $this->user_it, 
			$this->Utf8ToWin('Login') );

		$_REQUEST['Caption'] = $this->user_it->utf8towin($_REQUEST['Caption']);
		
		$this->user->modify_parms($this->user_it->getId(),
             array( 'Caption' => $_REQUEST['Caption'],
				    'Email' => $_REQUEST['Email'],
				    'Login' => $_REQUEST['Login'],
				    'ICQ' => $_REQUEST['ICQ'],
				    'Skype' => $_REQUEST['Skype'],
				    'Phone' => $_REQUEST['Phone'],
				    'Language' => $_REQUEST['Language'],
				    'Skills' => $_REQUEST['Skills'],
				    'Tools' => $_REQUEST['Tools'],
                    'NotificationTrackingType' => $_REQUEST['NotificationTrackingType'],
                    'NotificationEmailType' => $_REQUEST['NotificationEmailType']
				 )
			);

		$this->replySuccess( 
			$this->getResultDescription( 1001 ) );
	}

	function getResultDescription( $result )
	{
		switch($result)
		{
			case 1001:
				return text(187);

			default:
				return parent::getResultDescription( $result );
		}
	}
 }
 