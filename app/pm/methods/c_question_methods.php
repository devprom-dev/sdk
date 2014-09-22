<?php
 
 //////////////////////////////////////////////////////////////////////////////////////
 class ConvertQuestionWebMethod extends WebMethod
 {
 	var $question_it; 
 	
 	function ConvertQuestionWebMethod ( $question_it = null )
 	{
 		$this->question_it = $question_it;
 		
 		parent::WebMethod();
 	}
 	
	function getCaption() 
	{
		return text(747);
	}
	
	function hasAccess()
	{
		return getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('pm_ChangeRequest'));
	}
	
	function getUrl( $parms_array = array() )
	{
		return parent::getUrl( array('question' => $this->question_it->getId()) );
	}
	
	function getJSCall( $parms_array = array() )
	{
		return parent::getJSCall( array('question' => $this->question_it->getId()) );
	}

 	function execute_request()
 	{
 		global $model_factory, $_REQUEST;
 		
		$question = $model_factory->getObject('pm_Question');
		$question_it = $question->getExact($_REQUEST['question']);
		
 		echo '&Question='.$question_it->getId();
 	}
 	
 	function getRedirectUrl()
 	{
 		global $model_factory;

		$request = $model_factory->getObject('pm_ChangeRequest');
 		return $request->getPageNameObject();
 	}
 }
 
?>