<?php
 
 //////////////////////////////////////////////////////////////////////////////////////
 class CommentDeleteWebMethod extends WebMethod
 {
 	var $comment_it, $control_id;
 	
 	function CommentDeleteWebMethod ( $comment_it = null, $control = 0 )
 	{
 		$this->comment_it = $comment_it;
 		$this->control_id = $control;
 		
 		parent::WebMethod();
 	}
 	
	function getCaption() 
	{
		return translate('Удалить');
	}
	
	function hasAccess()
	{
 		$project_roles = getSession()->getRoles();
 		
 		return getFactory()->getAccessPolicy()->can_delete($this->comment_it) && ($project_roles['lead'] || 
 			$this->comment_it->get('AuthorId') == getSession()->getUserIt()->getId());
	}
	
	function getJSCall( $parms_array = array() )
	{
		return parent::getJSCall( array(
			'comment' => $this->comment_it->getId()
		));
	}

 	function execute_request()
 	{
 		global $model_factory, $_REQUEST;
 		
		$comment = $model_factory->getObject('Comment');
		$this->comment_it = $comment->getExact($_REQUEST['comment']);
		
		if ( $this->comment_it->getId() < 1 || !$this->hasAccess() ) return;
		
		$this->comment_it->delete();
 	}
 	
 	function getRedirectUrl()
 	{
 		return 'function(){ refreshCommentsThread(\''.$this->control_id.'\'); }';
 	}
 }
 
?>