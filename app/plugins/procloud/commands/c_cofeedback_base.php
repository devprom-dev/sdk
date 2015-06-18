<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_cofeedback_base.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 
 ////////////////////////////////////////////////////////////////////////////
 class CoFeedbackBase extends FeedbackBase
 {
 	function execute()
 	{
 		global $model_factory, $project_it, $user_it;
 		
 		parent::execute();
 		
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
 	}
 	
	function authenticate()
	{
		global $_REQUEST, $project_it, $model_factory;

	 	if ( $_REQUEST['key'] != '' )
	 	{
	 	 	if ( $_REQUEST['key'] != $project_it->getFeedbackAuthKey() )
	 	 	{
			 	return text('procloud645');
	 	 	}
	 	}
	 	else
	 	{
		 	 // check for answer on control question
			 $author = Utf8ToWin($_REQUEST['author']);
			 $answer = Utf8ToWin($_REQUEST['answer']);
			 $hash = $_REQUEST['hash'];
			
			 if ( $author == '' || $answer == '' || $hash == '' )
			 {
			 	return text('procloud643');
			 }
			 
			 $question = $model_factory->getObject('cms_CheckQuestion');
			 $check_result = $question->checkAnswer( $hash, $answer );
			 
			 if ( !$check_result )
			 {
			 	return text('procloud240');
			 }
		
			 // validate email
			 $email_reg_exp = '/^([a-zA-Z][\w\.-]*[a-zA-Z0-9])@([a-zA-Z0-9][\w\.-]*[a-zA-Z0-9])\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/';
			 $author_parts = array();
			 
			 if ( !preg_match($email_reg_exp, $author, $author_parts) || count($author_parts) < 2 )
			 {
			 	return text('procloud279');
			 }
	 	}

	 	return "";
	}
	
	function setUser()
	{
		global $model_factory, $_REQUEST, $user_it	;
		
		$author = Utf8ToWin($_REQUEST['author']);
		
		$user = $model_factory->getObject('cms_User');
		$user_it = $user->getByRef('LCASE(Email)', strtolower($author));
		
		if ( $user_it->count() < 1 )
		{
		 	// generate password
		 	$_REQUEST['OriginalPassword'] = $this->getPassword();
		 	$parts = preg_split('/@/', strtolower($author));
		
		 	$user_id = $user->add_parms(
		 		array(
		 			'Email' => $author,
		 			'Caption' => $parts[0],
		 			'Login' => $parts[0],
		 			'Password' => $_REQUEST['OriginalPassword'],
		 			'Language' => 1
		 			)
		 		);
		 		
		 	$user_it = $user->getExact($user_id);
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