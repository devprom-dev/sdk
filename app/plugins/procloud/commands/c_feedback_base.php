<?php

 ////////////////////////////////////////////////////////////////////////////
 class FeedbackBase extends Command
 {
 	function execute()
	{
		global $_REQUEST, $model_factory, $project, $project_it, $author;

		// project should be identified
		if ( $_REQUEST['project'] == '' )
		{
			echo $_REQUEST['callback'].'(';
			 	echo '{"error":"'.text('procloud633').'"}';
			echo ')';
		
		 	die();
		}
		 
		// check if the project exists
		$project = $model_factory->getObject('pm_Project');
		$object_it = $project->getByRef('CodeName', $_REQUEST['project']);
		 
		if ( $object_it->count() < 1 )
		{
			echo $_REQUEST['callback'].'(';
			 	echo '{"error":"'.text('procloud241').'"}';
			echo ')';
		
		 	die();
		}
		
		$session = new PMSession($object_it);
		
		$result = $this->authenticate();
		
		if ( $result != "" )
		{
 			echo $_REQUEST['callback'].'(';
			 	echo '{"error":"'.$result.'"}';
			echo ')';

		 	die();
		}
		 
		// setup environment
		$model_factory->object_factory->access_policy = new AccessPolicy;
		 
		// indentify a user who will be driver of a new issue or question
		$this->setUser();
		
		$user_it = getSession()->getUserIt();
		  
		if ( $user_it->count() < 1 || !$project_it->IsPublicChangeRequests() )
		{
			echo $_REQUEST['callback'].'(';
			 	echo '{"error":"'.text('procloud242').'"}';
			echo ')';
		
		 	die();
		}
	}
	
	function authenticate()
	{
		global $_SERVER;
		
	 	if ( $_SERVER['REMOTE_USER'] == '' && $_SERVER['REDIRECT_REMOTE_USER'] == '' )
	 	{
			return text('procloud635');
	 	}
	 	
	 	return "";
	}
	
	function setUser()
	{
		global $model_factory, $_SERVER, $project_it, $part_it;
		
		$user = $model_factory->getObject('cms_User');
		
		$login = $_SERVER['REMOTE_USER'] != '' ? 
			$_SERVER['REMOTE_USER'] : $_SERVER['REDIRECT_REMOTE_USER'];
	
		$user_it = $user->getByRefArray(
			array ( "IFNULL(t.Login, '-')" => $login ), 1
		);
	
		if ( $user_it->count() < 1 )
		{
		 	$part_it = $project_it->getLeadIt();
		 	$user_it = $part_it->getRef("SystemUser");
		}
	}
 }
 
?>