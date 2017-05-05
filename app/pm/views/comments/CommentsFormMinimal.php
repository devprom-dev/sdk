<?php

include_once 'CommentList.php';
include_once "FieldCheckNotifications.php";

class CommentsFormMinimal extends PMPageForm
{
	private $anchor_it;
	private $private = false;

	function extendModel()
	{
		parent::extendModel();
		$this->getObject()->addAttribute('Attachment', 'VARCHAR', '', true, false);
        $this->getObject()->addAttribute('Notification', 'VARCHAR', '', true, false);

        $objectIt = $this->getObjectIt();
        if ( is_object($objectIt) && $objectIt->getId() != '' ) {
            $this->private = $objectIt->get('IsPrivate') == 'Y';
        }
	}

	public function setAnchorIt( $anchor_it )
	{
		$this->anchor_it = $anchor_it;
	}
	
	function IsAttributeVisible( $attr_name )
	{
	    $attributes = array('Caption','Attachment');
	    if ( !$this->private ) $attributes[] = 'Notification';
		return in_array($attr_name, $attributes);
	}
	
	function getFieldValue( $attr )
	{
	    switch ( $attr )
	    {
	        case 'ObjectId':
	            return $this->anchor_it->getId();
	            
	        case 'ObjectClass':
	            return get_class($this->anchor_it->object);
	            
	        case 'AuthorId':
	        	return getSession()->getUserIt()->getId();
	            
	        default:
	            return parent::getFieldValue( $attr );
	    }
	}
		
	function createFieldObject( $attribute )
	{
		switch ( $attribute )
		{
		    case 'Caption':
                $field = new FieldWYSIWYG();
						
 				is_object($this->getObjectIt()) 
 					? $field->setObjectIt( $this->getObjectIt() ) : $field->setObject( $this->getObject() );

				$editor = $field->getEditor();
				$editor->setMode( WIKI_MODE_MINIMAL );

				$field->setHasBorder( false );
				$field->setName($attribute);
				return $field;

			case 'Attachment':
				$field = new FieldCommentAttachments( is_object($this->getObjectIt()) ? $this->getObjectIt() : $this->getObject() );
				$field->setAddButtonText(text(2081));
				return $field;

            case 'Notification':
                $field = new FieldCheckNotifications();
                $field->setAnchor($this->anchor_it);
                return $field;

		    default:
		    	return parent::createFieldObject( $attribute );
		}
	}

	function getRenderParms()
	{
		return array_merge( 
				parent::getRenderParms(), 
				array(
					'form_body_template' => "pm/CommentsFormMinimal.php"
				)
		);
	}

	function validateInputValues($id, $action)
    {
        if ( $this->private || $_REQUEST['Notification'] == '' ) {
            $this->getObject()->removeNotificator('ServicedeskCommentEmailNotificator');
        }

        $notificationSpecified = array_key_exists('Notification', $_REQUEST) || array_key_exists('NotificationOnForm', $_REQUEST);
        if ( $notificationSpecified && in_array($_REQUEST['Notification'], array('N','')) ) {
            $_REQUEST['IsPrivate'] = 'Y';
        }

        return parent::validateInputValues($id, $action);
    }

    function getThreadParms( $comment_it )
	{
		$field = new FieldWYSIWYG();
		
		while( !$comment_it->end() ) 
		{
	   		ob_start();
	   		
			$field->setObjectIt( $comment_it );
			$field->setValue( $comment_it->get('Caption') );
			$field->drawReadonly();
			
	   		$text = ob_get_contents();
	   		ob_end_clean();
			
			$comments[] = array (
	 				'id' => $comment_it->getId(),
	 				'author' => $comment_it->get('AuthorName'),
	 			    'author_id' => $comment_it->get('AuthorId'),
	 				'created' => $comment_it->getDateTimeFormat('RecordCreated'),
	 				'actions' => array(),
	 				'html' => $text,
	 				'thread_it' => $comment_it->getThreadIt(),
					'files' => array()
			);
			$comment_it->moveNext();
		}
		
		return array(
			'list' => $this,
			'comments' => $comments,
			'readonly' => true
		);
	}

	function renderThread( $view, $comment_it = null, $level = 0 )
	{
 		if ( !is_object($comment_it) )
 		{
			$comment = getFactory()->getObject('Comment');
			$comment->addSort(new SortAttributeClause('RecordCreated'));
			
			$comment_it = $comment->getAllRootsForObject($this->anchor_it);
			$comment_it->moveToPos(max(0,$comment_it->count() - 2));
 		}
 		
 		if ( $level > 50 || $comment_it->count() < 1 ) return;

		echo $view->render("pm/CommentsThread.php", 
			array_merge( array (
				'level' => $level
				), $this->getThreadParms($comment_it)
			) ); 
	}
}