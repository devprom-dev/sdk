<?php
use Devprom\ProjectBundle\Service\Email\CommentNotificationService;
use Devprom\ProjectBundle\Service\Model\ModelChangeNotification;
include_once 'CommentsThread.php';
include_once "FieldCheckNotifications.php";

class CommentsFormMinimal extends PMPageForm
{
	private $anchor_it;
	private $commentIt = null;
	private $private = false;

	function extendModel()
	{
		parent::extendModel();
		$this->getObject()->addAttribute('Attachment', 'VARCHAR', '', true, false);
        $this->getObject()->addAttribute('Notification', 'VARCHAR', '', true, false);
        $this->getObject()->removeAttribute('Project');

        $objectIt = $this->getObjectIt();
        if ( is_object($objectIt) && $objectIt->getId() != '' ) {
            $this->private = $objectIt->get('IsPrivate') == 'Y';
        }

        if ( $_REQUEST['ObjectId'] != '' && $_REQUEST['ObjectClass'] != '' ) {
            $className = getFactory()->getClass($_REQUEST['ObjectClass']);
            if ( class_exists($className) ) {
                $this->anchor_it = getFactory()->getObject($className)->getExact($_REQUEST['ObjectId']);
            }
        }
	}

	public function setObjectIt($object_it) {
	    if ( is_object($object_it) ) {
            $this->anchor_it = $object_it->getAnchorIt();
        }
        parent::setObjectIt($object_it);
    }

    public function setCommentIt($commentIt) {
        $this->anchor_it = $commentIt->getAnchorIt();
	    $this->commentIt = $commentIt->getRollupIt();
    }

    public function getCommentIt() {
	    return $this->commentIt;
    }

    public function setAnchorIt( $anchor_it ) {
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
                $options = new CommentNotificationService($this->anchor_it);
                $field = new FieldCheckNotifications();
                $field->setEmails($options->getEmails());
                return $field;

		    default:
		    	return parent::createFieldObject( $attribute );
		}
	}

	function getRenderParms()
	{
        $service = new ModelChangeNotification();
        $service->clearUser($this->anchor_it, getSession()->getUserIt());

		return array_merge( 
            parent::getRenderParms(),
            array(
                'form_body_template' => "pm/CommentsFormMinimal.php"
            )
		);
	}

    function getThreadParms( $comment_it )
	{
		$field = new FieldWYSIWYG();
		
        ob_start();

        $field->setObjectIt( $comment_it );
        $field->setValue( $comment_it->get('Caption') );
        $field->drawReadonly();

        $text = ob_get_contents();
        ob_end_clean();

        $commentData = array (
            'id' => $comment_it->getId(),
            'uid' => md5(get_class($this).$comment_it->getId()),
            'author' => $comment_it->get('AuthorName'),
            'author_id' => $comment_it->get('AuthorId'),
            'created' => $comment_it->getDateTimeFormat('RecordCreated'),
            'actions' => array(),
            'html' => $text,
            'text' => \TextUtils::stripAnyTags($text),
            'files' => array()
        );

        $comment_it->moveNext();
        if ( !$comment_it->end() ) {
            $commentData['thread_it'] = $comment_it;
        }
        $comments[] = $commentData;

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
			$comment_it->moveToPos(max(0,$comment_it->count() - 1));
 		}
 		
 		if ( $level > 50 || $comment_it->count() < 1 ) return;

		echo $view->render("pm/CommentsThread.php",
			array_merge( array (
				'level' => $level
				), $this->getThreadParms($comment_it)
			) ); 
	}
}