<?php

 ////////////////////////////////////////////////////////////////////////////
 class CreateUser extends Command
 {
 	var $user_it;
 	
 	function validate()
 	{
 		global $_REQUEST, $model_factory;

		// check the correctness of the credentials of the first participant
		if($_REQUEST['Login'] == '' || $_REQUEST['Password'] == '' ||
			$_REQUEST['Email'] == '' || $_REQUEST['Password2'] == '' ) return 1;

		// проверим корректность задания пароля
		if($_REQUEST['Password'] != $_REQUEST['Password2']) return 2;
		
		// проверим корректность задания логина	
		if(strpos($_REQUEST['Login'], '@') !== false) return 3;

		// проверим подпись пользовательского соглашения	
		if( $_REQUEST['Agreement'] != 'yes' ) return 6;

		// check for a valid email
		if ( !preg_match ("/^[\w\.-]{1,}\@([\da-zA-Z-]{1,}\.){1,}[\da-zA-Z-]+$/", $_REQUEST['Email']) ) return 8;
		list($user, $domain) = preg_split('/@/', $_REQUEST['Email']);
		
		if( $_REQUEST['Agreement'] != 'yes' ) return 6;

		// проверим на уникальность email
		$user_cls = new Metaobject('cms_User');
		$user_it = $user_cls->getByRef('LCASE(Email)', strtolower($_REQUEST['Email']) );
			
		if($user_it->count() > 0) return 4;

		// проверим на уникальность login
		$user_cls = new Metaobject('cms_User');
		$user_it = $user_cls->getByRef('LCASE(Login)', strtolower($_REQUEST['Login']) );
			
		if($user_it->count() > 0) return 5;
		
 		return 0;
 	}
 	
 	function execute()
	{
		global $_REQUEST, $factory;

		// отключаем VPD
		$factory =& getModelFactory();
		
		$factory->enableVpd(false);
		
		$result = $this->validate();
		if( $result > 0 ) 
		{
			$this->replyError($this->getResultDescription( $result ));
		}

		// создаем пользователя
		$user_cls = $factory->getObject('cms_User');
		
		$_REQUEST['Login'] = $this->Utf8ToWin($_REQUEST['Login']);
		$_REQUEST['Password'] = $this->Utf8ToWin($_REQUEST['Password']);
		
		$parms['Caption'] = $_REQUEST['Login'];
		$parms['Login'] = $_REQUEST['Login'];
		$parms['Email'] = $_REQUEST['Email'];
		$parms['Password'] = $_REQUEST['Password'];
		$parms['Language'] = $_REQUEST['Language'];
		
		$user_id = $user_cls->add_parms( $parms );
		$this->user_it = $user_cls->getExact($user_id);
						
		// report result of the operation
		//
		$this->replySuccess( $this->getResultDescription( -1 ) );
	}
	
	function getUserIt()
	{
		return $this->user_it;
	}
	
	function getResultDescription( $result )
	{
		switch($result)
		{
			case -1:
				return text(65);
			case 1:
				return text(210);
			case 2:
				return text(211);
			case 3:
				return text(212);
			case 4:
				return text(213);
			case 5:
				return text(214);
			case 6:
				return text('procloud215');
			case 8:
				return text(515);
			case 9:
				return text(516);
		}
	}
 }
 
?>