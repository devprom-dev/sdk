<?php
 
class CommentDeleteWebMethod extends WebMethod
{
    var $comment_it, $control_id;
 	
 	function __construct ( $comment_it = null, $control = 0 )
 	{
 		$this->comment_it = $comment_it;
 		$this->control_id = $control;
 		parent::__construct();
 	}
 	
	function getCaption() {
		return translate('Удалить');
	}

	function hasAccess()
	{
 		$project_roles = getSession()->getRoles();
 		
 		return getFactory()->getAccessPolicy()->can_delete($this->comment_it) && ($project_roles['lead'] || 
 			$this->comment_it->get('AuthorId') == getSession()->getUserIt()->getId());
	}
	
	function getJSCall( $parms = array() )
	{
		return parent::getJSCall( array(
			'comment' => $this->comment_it->getId()
		));
	}

 	function execute_request()
 	{
		$comment = getFactory()->getObject('Comment');
		$this->comment_it = $comment->getExact($_REQUEST['comment']);
		
		if ( $this->comment_it->getId() < 1 || !$this->hasAccess() ) return;
		
		$this->comment_it->delete();

        if ( class_exists('UndoWebMethod') && UndoLog::Instance()->valid($this->comment_it) ) {
            $method = new UndoWebMethod(ChangeLog::getTransaction());
            $method->setCookie();
        }
 	}
 	
 	function getRedirectUrl()
 	{
 		return 'function(){ refreshCommentsThread(\''.$this->control_id.'\'); }';
 	}
}