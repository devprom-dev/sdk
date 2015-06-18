<?php

include_once SERVER_ROOT_PATH.'pm/methods/c_comment_methods.php';
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";

include_once 'CommentForm.php';

class CommentList 
{
 	var $object_it, $object, $control_uid, $attachment_it, $comments;
 	private $uid_service = null;
 	
 	private $baseline = '';
 	
 	function CommentList( $object_it, $baseline = '' ) 
 	{
 		global $model_factory;
 		
 		$this->object_it = $object_it;
 		
 		$this->baseline = $baseline;
 		
 		$this->object = $model_factory->getObject('Comment');
 		
 		if ( $this->baseline != '' )
 		{
 			$snapshot = $model_factory->getObject('Snapshot');
 			
 			$this->object->addFilter( new SnapshotBeforeDatePredicate($this->baseline) );
 		}
 		
 		$this->object->addAttribute( 'Attachment', 'REF_pm_AttachmentId', '', false );
 		
 		$this->object->addPersister( new AttachmentsPersister() );

 		$this->url = '?export=commentsthread&object='.$this->object_it->getId().'&objectclass='.get_class($this->object_it->object);

		$this->object->defaultsort = 'RecordCreated ASC';

		$this->comment_it = $this->object->getAllRootsForObject($this->object_it);
		
		$this->comments = 0;
		
		$this->control_uid = md5($this->object_it->object->getClassName().$this->object_it->getId());
		$this->uid_service = new ObjectUID();
 	}
 	
 	function setControlUID( $uid )
 	{
 		$this->control_uid = $uid;
 	}

 	function getComments()
 	{
 		return $this->comments;
 	}
 	
 	function drawComment( $comment_it )
 	{
		$field = new FieldWYSIWYG();
 						
		$field->setObjectIt( $comment_it );
		$field->setValue( $comment_it->get('Caption') );
				
		$field->drawReadonly();
 	}
 	
	function getActions( $object_it )
	{
		$actions = array();
		
		if ( $object_it->get('AuthorId') == getSession()->getUserIt()->getId() )
		{
			$actions[] = array ();
			$actions[] = array (
				'url' => 'javascript: showCommentForm(\''.$this->url.
					'\',$(\'#commentsreply'.$object_it->getId().'\'), \''.$object_it->getId().'\', \'\');',
				'name' => translate('Изменить') 
			);
		}

		$method = new CommentDeleteWebMethod( $object_it, $this->control_uid );
		if ( $method->hasAccess() )
		{
			$actions[] = array ();
			$actions[] = array (
				'url' => $method->getJSCall(),
				'name' => $method->getCaption() 
			);
		}
		
		return $actions;
	}
	
	function drawMenu( $actions = array() )
	{
		if ( count($actions) < 1 ) return;
		
		$popup = new PopupMenu();
		$popup->draw( "list_row_popup", '<img src="/images/bullet_edit.png">', $actions ); 
	}
	
 	function getRenderParms()
	{
		$form = new CommentForm( getFactory()->getObject('Comment') );
		
		$form->setAnchorIt( $this->object_it );
		$form->setControlUID( $this->control_uid );	
		
		return array(
			'list' => $this,
			'form' => $form,
			'control_uid' => $this->control_uid,
			'url' => $this->url,
			'form_ready' => $_REQUEST['formonly'] == 'true' && $_REQUEST['entity'] == 'Comment',
			'comments_count' => $this->comment_it->count()
		);
	}
	
	function render( $view, $parms )
	{
		echo $view->render("pm/CommentsList.php", 
			array_merge($parms, $this->getRenderParms()) ); 
	}	
	
 	function getThreadRenderParms( $comment_it )
	{
		global $model_factory;

		$comments = array();
		
 		do {
	   		ob_start();
	   		
	   		$this->drawComment( $comment_it );
	   		$text = ob_get_contents();
	   		
	   		ob_end_clean();
	   		
	        $files = array();
	        
	        $file_it = $comment_it->getRef('Attachment');
	        
	        while( !$file_it->end() )
	        {
	            $files[] = array (
	                    'type' => $file_it->IsImage('File') ? 'image' : 'file',
	                    'url' => $file_it->getFileUrl(),
	                    'name' => $file_it->getFileName('File'),
	                    'size' => $file_it->getFileSizeKb('File')
	            );  
	            
	            $file_it->moveNext();
	        }
	        	        
 			$comments[] = array (
 				'id' => $comment_it->getId(),
 				'author' => $comment_it->get('AuthorName'),
 			    'author_id' => $comment_it->get('AuthorId'),
 				'created' => $comment_it->getDateTimeFormat('RecordCreated'),
 				'actions' => $this->getActions( $comment_it ),
 				'html' => $text,
 				'thread_it' => $comment_it->getThreadIt(),
 			    'files' => $files,
 				'uid_info' => $this->uid_service->getUidInfo($comment_it)
 			);
 			
 			$comment_it->moveNext();
 		}
 		while ( !$comment_it->end() );		
		
		return array(
			'list' => $this,
			'form' => $form,
			'control_uid' => $this->control_uid,
			'url' => $this->url,
			'comments' => $comments,
			'readonly' => false
		);
	}
	
	function renderThread( $view, $comment_it = null, $level = 0 )
	{
 		if ( !is_object($comment_it) ) $comment_it = $this->comment_it;
 		
 		if ( $level > 50 || $comment_it->count() < 1 ) return;

		echo $view->render("pm/CommentsThread.php", 
			array_merge( array (
				'level' => $level
				), $this->getThreadRenderParms($comment_it)
			) ); 
	}
}
