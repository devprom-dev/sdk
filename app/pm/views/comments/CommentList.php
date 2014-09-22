<?php

include_once SERVER_ROOT_PATH.'pm/methods/c_comment_methods.php';
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";

include_once 'CommentForm.php';

class CommentList 
{
 	var $object_it, $object, $control_uid, $attachment_it, $comments;
 	
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
		global $project_it;
		
		$actions = array();
		
		$actions[] = array (
			'url' => '/pm/'.$project_it->get('CodeName').'/O-'.$object_it->getId(),
			'name' => translate('—сылка') 
		);
		
		if ( $object_it->get('AuthorId') == getSession()->getUserIt()->getId() )
		{
			$actions[] = array ();
			$actions[] = array (
				'url' => 'javascript: showCommentForm(\''.$this->url.
					'\',$(\'#commentsreply'.$object_it->getId().'\'), \''.$object_it->getId().'\', \'\');',
				'name' => translate('»зменить') 
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
		global $model_factory;
		
		$form = new CommentForm( $model_factory->getObject('Comment') );
		
		$form->setAnchorIt( $this->object_it );
		$form->setControlUID( $this->control_uid );	
		
		return array(
			'list' => $this,
			'form' => $form,
			'control_uid' => $this->control_uid,
			'url' => $this->url,
			'form_ready' => $_REQUEST['formonly'] == 'true'
		);
	}
	
	function render( &$view, $parms )
	{
		echo $view->render("pm/CommentsList.php", 
			array_merge($parms, $this->getRenderParms()) ); 
	}	
	
 	function getThreadRenderParms( $comment_it )
	{
		global $model_factory;

		$comments = array();
		
 		do {
	   		if ( $comment_it->get('AuthorId') > 0 )
	   		{
				$author_it = $comment_it->getRef('AuthorId');
				
				$title = $author_it->getDisplayName();
	   		}
	   		else
	   		{
	   		    $author_it = null;
	   		    
				$externalEmail = $comment_it->get('ExternalEmail');
                $externalName = $comment_it->get('ExternalAuthor');
                if ($externalName) {
                    $title = sprintf("%s &lt;%s&gt;", $externalName, $externalEmail);
                } else {
                    $title = $externalEmail;
                }
	   		}
	   		
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
 				'author' => $title,
 			    'author_id' => is_object($author_it) ? $author_it->getId() : '',
 				'created' => $comment_it->getDateTimeFormat('RecordCreated'),
 				'actions' => $this->getActions( $comment_it ),
 				'html' => $text,
 				'thread_it' => $comment_it->getThreadIt(),
 			    'files' => $files
 			);
 			
 			$comment_it->moveNext();
 		}
 		while ( !$comment_it->end() );		
		
		return array(
			'list' => $this,
			'form' => $form,
			'control_uid' => $this->control_uid,
			'url' => $this->url,
			'comments' => $comments
		);
	}
	
	function renderThread( &$view, $comment_it = null, $level = 0 )
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
