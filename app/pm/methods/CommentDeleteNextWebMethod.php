<?php
 
class CommentDeleteNextWebMethod extends WebMethod
{
    var $comment_it, $control_id;
 	
 	function __construct ( $comment_it = null, $control = 0 )
 	{
 		$this->comment_it = $comment_it;
		$this->control_id = $control;
 		parent::__construct();
 	}
 	
	function getCaption() {
		return text(2185);
	}

	function hasAccess()
	{
 		$project_roles = getSession()->getRoles();
 		return getFactory()->getAccessPolicy()->can_delete($this->comment_it) && $project_roles['lead'];
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

		$comment_it = $comment->getRegistry()->Query(
			array (
				new CommentRootFilter(),
				new FilterAttributePredicate('ObjectId', $this->comment_it->get('ObjectId')),
				new FilterAttributePredicate('ObjectClass', $this->comment_it->get('ObjectClass')),
				new SortKeyClause()
			)
		);
		$comment_it->moveToId($this->comment_it->getId());
		$comment_it->moveNext();
		if ( $comment_it->getId() < 1 ) return;

		while( !$comment_it->end() ) {
			$comment->delete($comment_it->getId());
			$comment_it->moveNext();
		}

        if ( class_exists('UndoWebMethod') && UndoLog::Instance()->valid($this->comment_it) ) {
            $method = new UndoWebMethod(ChangeLog::getTransaction());
            $method->setCookie();
        }
 	}

 	function getRedirectUrl() {
 		return 'function(){ refreshCommentsThread(\''.$this->control_id.'\'); }';
 	}
}