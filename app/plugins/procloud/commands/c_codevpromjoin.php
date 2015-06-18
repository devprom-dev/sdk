<?php
 
 include( 'c_cojoin.php' );
 include( dirname(__FILE__).'/../crypto/cryptographp.fct.php');

 include_once "UserPasswordHashService.php";
 
 class CoDevpromJoin extends CoJoin
 {
 	function validate()
 	{
 		global $model_factory, $_REQUEST;
 		
		$this->command = new CreateUser;
 		
		$this->checkRequired( 
			array('Firstname', 'Email', 'Phone', 'Company', 'Possibilities', 'Captcha') );
		
		// check for captcha
		if ( !chk_crypt( $_REQUEST['Captcha'] ) )
		{
			$this->replyError( 'Введенный Вами проверочный код не соответствует изображенному тексту.' );
		}
			
		// check for a valid email
		if ( !preg_match ("/^[\w\.-]{1,}\@([\da-zA-Z-]{1,}\.){1,}[\da-zA-Z-]+$/", $_REQUEST['Email']) ) 
		{
			$this->replyError( 'Пожалуйста, укажите существующий электронный адреc' );
		}
		
		$user = $model_factory->getObject('cms_User');
		$user_it = $user->getByRefArray( array (
			'Email' => strtolower(trim($_REQUEST['Email']))
		));

		if ( $user_it->getId() > 0 )
		{
			return $this->replyError( 'Указанный адрес электронной почты уже используется, пожалуйста, выполните <a href="javascript: getLoginForm($(\'#loginRedirectUrl\').val());">авторизацию</a>' );
		}
		
		return true;
 	}
 	
 	function create()
	{
		global $model_factory, $_REQUEST;
		
		$user_cls = $model_factory->getObject('cms_User');
		$_REQUEST['remember'] = 'on';

		$parms['Caption'] = $this->Utf8ToWin($_REQUEST['Firstname']);
		
		$parms['Email'] = strtolower(trim($_REQUEST['Email']));
		$parts = preg_split('/@/', $parms['Email']);
		$parms['Login'] = $parts[0];
		
		$parms['Password'] = $this->getPassword();
		$parms['Language'] = '1';
		
		$company = $this->Utf8ToWin($_REQUEST['Company']);

		$possibilties = $this->Utf8ToWin($_REQUEST['Possibilities']);
		
		$parms['Description'] = 
			'Компания: '.$company.chr(10).
			'Интересует: '. $possibilties.chr(10);

		$parms['Phone'] = $_REQUEST['Phone'];
		
		$user_id = $user_cls->add_parms( $parms );

		if ( $user_id > 0 )
		{
			$user_it = $user_cls->getExact($user_id);

			$service = new UserPasswordHashService();
			
			$service->storePassword($user_it, $service->getHash($parms['Password'], $_REQUEST['lru'])); 
			
			getSession()->open( $user_it );
			
			$this->replySuccess( 'Через несколько секунд начнется загрузка выбранного файла. '.
				'На указанный электронный адрес выслано письмо с паролем для загрузки других файлов' );
		}
		else
		{
			$this->replyError( $this->getResultDescription( 9 ) );
		}
	}
	
	function getResultDescription( $result )
	{
		global $_REQUEST;
		
		if ( $result == -1 && $_REQUEST['skip'] == 'info' )
		{
			return ' > ';
		}
		else
		{
			return $this->command->getResultDescription( $result );
		}
	}
	
	function getPassword( $length = 8 )
	{
 		$conso=array("b","c","d","f","g","h","j","k","l","m","n","p","r","s","t","v","w","x","y","z"); 
    	$vocal=array("a","e","i","o","u");
    	 
    	$password=""; 
    	srand ((double)microtime()*1000000); 
    	$max = $length/2; 
	    for($i=1; $i<=$max; $i++) 
	    { 
	    	$password.=$conso[rand(0,19)]; 
	    	$password.=$vocal[rand(0,4)]; 
	    } 
    	return $password; 		
	}
 }
 
?>