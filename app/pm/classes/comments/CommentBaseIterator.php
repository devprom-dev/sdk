<?php

include_once SERVER_ROOT_PATH.'ext/html/html2text.php';

class CommentBaseIterator extends OrderedIterator
{
 	function getPlainText( $attr )
 	{
		$totext = new html2text( html_entity_decode($this->get_native($attr), ENT_QUOTES | ENT_HTML401, APP_ENCODING) );
		
		return $totext->get_text();
 	}
 	
 	function getThreadCommentsIt() 
 	{
 		$this->object->defaultsort = 'RecordCreated ASC';
 		return $this->object->getByRef2('ObjectId', $this->get('ObjectId'), 
			'LCASE(ObjectClass)', strtolower($this->get('ObjectClass')) );
 	}
 
 	function getThreadEmails( $emails = array() )
 	{
		$comment_it = $this->getRollupIt();
		
		while ( !$comment_it->end() ) 
		{
			if( $comment_it->get('AuthorEmail') != '' ) 
			{
				if(in_array($comment_it->get('AuthorEmail'), $emails) === false) 
				{
					array_push( $emails, $comment_it->get('AuthorEmail') );
				}
			}
			
			$comment_it->moveNext();
		}
		
		return $emails;
 	}

 	function getThreadExternalEmails()
 	{
 		global $model_factory, $project_it;
 		
		$comment_it = $this->getRollupIt();
		$ids = $comment_it->idsToArray();
		
		$sql = " SELECT u.* " .
			   "   FROM cms_User u, Comment c " .
			   "  WHERE c.CommentId IN (".join(',', $ids).") " .
			   "    AND u.cms_UserId = c.AuthorId ".
			   "	AND NOT EXISTS (SELECT 1 FROM pm_Participant p " .
			   "					 WHERE p.Project = ".$project_it->getId().
			   "    				   AND p.SystemUser = c.AuthorId" .
			   "					   AND p.IsActive = 'Y')";
		
		$user = $model_factory->getObject('cms_User');
		$user_it = $user->createSQLIterator( $sql );
		
		if ( $user_it->count() < 1 )
		{
			return array();
		}
		else
		{
			return $user_it->fieldToArray('Email');
		}
 	}

	function getLastCommentIt() 
	{
 		$this->object->defaultsort = 'RecordCreated DESC';
 		return $this->object->getByRefArray(
			array( 'ObjectId' => $this->get('ObjectId'), 
				   'LCASE(ObjectClass)' => strtolower($this->get('ObjectClass')) ), 
			1 );
	}
	
	function getThreadIt()
	{
		return $this->object->getRegistry()->Query(
				array (
						new FilterAttributePredicate('PrevComment', $this->getId()),
						new SortAttributeClause('RecordCreated')
				)
		);
	}
	
	function getRollupIt()
	{
		$comment_array = array();
		$comment_it = $this;
		
		while( $comment_it->getId() > 0 )
		{
			$comment_array[] = $comment_it->getData();
			if ( $comment_it->get('PrevComment') < 1 ) break;

			$comment_it = $comment_it->object->getRegistry()->Query(
				array ( new FilterInPredicate($comment_it->get('PrevComment')) )
			);
		}
		
		return $this->object->createCachedIterator($comment_array);
	}
	
	function getAnchorIt()
	{
	    $class_name = getFactory()->getClass($this->get('ObjectClass'));
	    
	    if ( !class_exists($class_name) ) return null;
	    
	    return getFactory()->getObject($class_name)->getExact($this->get('ObjectId'));
	}
	
	function getViewUrl()
	{
		global $project_it;
		
		$this->anchor_it = $this->getAnchorIt();

		if ( isset($project_it) && $project_it->count() > 0 )
		{ 		
			return '/pm/'.$project_it->get('CodeName').'/O-'.$this->getId();		
		}
		else
		{
			return _getServerUrl().'O-'.$this->getId();		
		}
	}
}