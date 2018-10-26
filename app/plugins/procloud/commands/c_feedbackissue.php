<?php

 require_once (dirname(__FILE__).'/c_feedback_base.php');

 ////////////////////////////////////////////////////////////////////////////
 class FeedbackIssue extends Command
 {
 	function getProxy()
 	{
 		return new FeedbackBase;
 	}
 	
 	function execute()
	{
		global $_REQUEST, $model_factory, $project_it, 
			   $_FILES, $controller;

		// process common settings and verifications
		$proxy = $this->getProxy();
		$proxy->execute();
		
		// prepare to submit issue
		$description = ($_REQUEST['description']);
		$kind = strtolower($_REQUEST['kind']);
		
		if ( $description == '' )
		{
			echo $_REQUEST['callback'].'(';
			 	echo '{"error":"'.text(634).'"}';
			echo ')';
		
		 	die();
		}

		if ( mb_detect_encoding($description, 'UTF-8', true) == 'UTF-8' )
		{
			$description = $project_it->utf8towin($description);
		}
		
		if ( $kind != 'bug' && $kind != 'enhancement' )
		{
		 	$kind = '';
		}
		 
		$type = $model_factory->getObject('pm_IssueType');
		$type_it = $type->getByRef('LCASE(ReferenceName)', $kind);

		$parms = array (
			'Project' => $project_it->getId(),
			'Caption' => $project_it->getWordsOnlyValue( $description, 20 ),
			'Description' => $description,
			'Priority' => 3,
			'Type' => $type_it->getId(),
			'SubmittedVersion' => $_REQUEST['version'],
			'Author' => $user_it->getId() );
		  
		$issue = $model_factory->getObject('pm_ChangeRequest');
		 
		$issue_it = $issue->getByRefArray(
		 	array ( 'Project' => $project_it->getId(),
		 			'Description' => $description )
		 	);
		 
		if ( $issue_it->count() < 1 )
		{	
			 $issue_id = $issue->add_parms( $parms );
			 $issue_it = $issue->getExact($issue_id);
		}
		 
		if ( $_FILES['attach']['tmp_name'] != '' )
		{
		 	$_FILES['File']['tmp_name'] = $_FILES['attach']['tmp_name'];
		 	$_FILES['File']['name'] = $_FILES['attach']['name'];
		 	$_FILES['File']['type'] = $_FILES['attach']['type'];
		 	
		 	$attachment = $model_factory->getObject('pm_Attachment');
		 	$attachment->add_parms(
		 		array( 'ObjectId' => $issue_it->getId(),
		 			   'ObjectClass' => 'pm_ChangeRequest' ) 
		 		);
		}
		 
		$index = 1;
		while ( $_FILES['attach'.$index]['tmp_name'] != '' )
		{
		 	$_FILES['File']['tmp_name'] = $_FILES['attach'.$index]['tmp_name'];
		 	$_FILES['File']['name'] = $_FILES['attach'.$index]['name'];
		 	$_FILES['File']['type'] = $_FILES['attach'.$index]['type'];
		 	
		 	$attachment = $model_factory->getObject('pm_Attachment');
		 	$attachment->add_parms(
		 		array( 'ObjectId' => $issue_it->getId(),
		 			   'ObjectClass' => 'pm_ChangeRequest' ) 
		 		);
		 		
		 	$index++;
		}
		 
		echo $_REQUEST['callback'].'(';
		 	echo '{"issue":"'.$controller->getGlobalUrl($issue_it).'"}';
		echo ')';
		 
		return $issue_it;
	}
 }
 
?>