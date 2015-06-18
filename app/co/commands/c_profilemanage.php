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
				   'Email' => $this->Utf8ToWin($_REQUEST['Email']),
				   'Login' => $this->Utf8ToWin($_REQUEST['Login']),
				   'ICQ' => $this->Utf8ToWin($_REQUEST['ICQ']),
				   'Skype' => $this->Utf8ToWin($_REQUEST['Skype']),
				   'Phone' => $this->Utf8ToWin($_REQUEST['Phone']),
				   'Language' => $this->Utf8ToWin($_REQUEST['Language']),
				   'Skills' => $this->Utf8ToWin($_REQUEST['Skills']),
				   'Tools' => $this->Utf8ToWin($_REQUEST['Tools'])
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
 