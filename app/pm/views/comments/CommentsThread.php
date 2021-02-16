<?php
use Devprom\ProjectBundle\Service\Email\CommentNotificationService;
include_once SERVER_ROOT_PATH . 'pm/methods/CommentDeleteWebMethod.php';
include_once SERVER_ROOT_PATH . 'pm/methods/CommentDeleteNextWebMethod.php';
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once 'CommentForm.php';

class CommentsThread
{
 	var $object_it, $object, $control_uid, $comments;
 	private $uid_service = null;
    private $autorefresh = true;
 	private $sortOrder = 'asc';
 	private $baseline = '';
 	private $options = null;
 	
 	function __construct( $object_it, $baseline = '' )
 	{
 		$this->object_it = $object_it;
 		$this->baseline = $baseline;
 		$this->object = getFactory()->getObject('Comment');
 		
 		if ( $this->baseline != '' ) {
 			$snapshot = getFactory()->getObject('Snapshot');
 			$this->object->addFilter( new SnapshotBeforeDatePredicate($this->baseline) );
 		}
 		$this->object->addAttribute( 'Attachment', 'REF_pm_AttachmentId', '', false );
		$this->object->addPersister(new AttachmentsPersister());

        $this->sortOrder = $_COOKIE['sort-comments'] == "-1" && !$this->object_it->object instanceof WikiPage
            ? 'desc' : 'asc';

        $queryParms = array(
            new AttachmentsPersister()
        );
        if ( $this->sortOrder == 'desc' ) {
            $queryParms[] = new SortRecentModifiedClause();
        }
		$this->comment_it = $this->object->getAllRootsForObject($this->object_it, $queryParms);

		$this->comments = 0;

		$this->control_uid = md5($this->object_it->object->getClassName().$this->object_it->getId().$_REQUEST['formonly']);
		$this->uid_service = new ObjectUID();
        $this->autorefresh = array_key_exists('dorefresh', $_REQUEST) ? $_REQUEST['dorefresh'] == 1 : true;
        $this->options = new CommentNotificationService($this->object_it);
 	}
 	
 	function setControlUID( $uid ) {
 		$this->control_uid = $uid;
 	}

 	function getControlUID() {
        return $this->control_uid;
    }

    function setAutoRefresh( $value = true ) {
        $this->autorefresh = $value;
    }

 	function getComments() {
 		return $this->comments;
 	}

 	function getSelfUrl() {
        return getSession()->getApplicationUrl($this->object_it).
            '?'.http_build_query(
                    array (
                        'export' => 'commentsthread',
                        'object' => $this->object_it->getId(),
                        'objectclass' => get_class($this->object_it->object),
                        'dorefresh' => $this->autorefresh,
                        'formonly' => \SanitizeUrl::parseUrl($_REQUEST['formonly'])
                    )
                );
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

        $deleteMethod = new CommentDeleteWebMethod( $object_it, $this->control_uid );
		if ( !$deleteMethod->hasAccess() ) return $actions;

        $method = new ObjectModifyWebMethod($object_it);
        $method->setObjectUrl(
            getSession()->getApplicationUrl($object_it).'comments/'.
                strtolower(get_class($this->object_it->object)).'/'.$this->object_it->getId().'?action=show'
        );
        $actions[] = array (
            'name' => $method->getCaption(),
            'url' => $method->getJSCall()
        );

        $method = new ModifyAttributeWebMethod($object_it, 'Closed', $object_it->get('Closed') == 'Y' ? 'N' : 'Y');
        $method->setCallback('function(){ refreshCommentsThread(\''.$this->control_uid.'\'); }');
        $actions[] = array (
            'name' => $object_it->get('Closed') == 'Y' ? translate('Открыть') : translate('Завершить'),
            'url' => $method->getJSCall()
        );

        $actions[] = array ();
        $actions[] = array (
            'click' => $deleteMethod->getJSCall(),
            'name' => $deleteMethod->getCaption()
        );

		if ( $object_it->get('PrevComment') == '' ) {
			$method = new CommentDeleteNextWebMethod( $object_it, $this->control_uid );
            $actions[] = array ();
            $actions[] = array (
                'click' => $method->getJSCall(),
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
			'url' => $this->getSelfUrl(),
			'form_ready' => $_REQUEST['formonly'] == 'true' && $_REQUEST['entity'] == 'Comment',
			'comments_count' => $this->comment_it->count(),
            'sort' => $this->sortOrder,
            'object_it' => $this->object_it,
            'public_comment' => true,
            'private_comment' => count($this->options->getEmails()) > 0,
            'options' => $this->getListOptions(),
            'optionsDefault' => array('order', 'open')
        );
	}
	
	function render( $view, $parms )
	{
		echo $view->render("pm/CommentsList.php", 
			array_merge($parms, $this->getRenderParms()) ); 
	}	
	
 	function getThreadRenderParms( $comment_it )
	{
	    $readonly = !getFactory()->getAccessPolicy()->can_modify($comment_it->object);
	    $privateEnabled = $comment_it->get('IsPrivate') != 'Y' && count($this->options->getEmails()) > 0;

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

	        $attributes = array();
	        if ( $comment_it->get('Closed') == 'Y' ) {
                $attributes[] = 'closed';
            }
	        else {
                $attributes[] = 'open';
            }
	        if ( $comment_it->get('AuthorId') == getSession()->getUserIt()->getId() ) {
                $attributes[] = 'mine';
            }
	        if ( $comment_it->get('IsNew') > 0 ) {
                $attributes[] = 'new';
            }

 			$comments[] = array (
 				'id' => $comment_it->getId(),
                'uid' => md5($this->control_uid.$comment_it->getId()),
 				'author' => $comment_it->get('AuthorName'),
 			    'author_id' => $comment_it->get('AuthorId'),
				'photo_id' => $comment_it->get('AuthorPhotoId'),
 				'created' => $comment_it->getDateFormattedShort('RecordCreated').', '.$comment_it->getTimeFormat('RecordCreated'),
 				'actions' => $readonly ? array() : $this->getActions( $comment_it ),
 				'html' => $text,
                'text' => \TextUtils::stripAnyTags($text),
 				'thread_it' => $comment_it->getThreadIt(),
 			    'files' => $files,
 				'uid_info' => $_REQUEST['formonly'] != '' ? '' : $this->uid_service->getUidInfo($comment_it),
                'modified' => strtotime($comment_it->get('RecordCreated')),
                'private' => $comment_it->get('IsPrivate') == 'Y',
                'closed' => $comment_it->get('Closed') == 'Y',
                'attributes' => join(' ', $attributes)
 			);
 			
 			$comment_it->moveNext();
 		}
 		while ( !$comment_it->end() );		
		
		return array(
			'list' => $this,
			'control_uid' => $this->control_uid,
			'url' => $this->getSelfUrl(),
			'comments' => $comments,
			'readonly' => $readonly,
            'private_comment' => $privateEnabled
		);
	}

	function getListOptions()
    {
        $options = array();
        $stateIt = getFactory()->getObject('CommentState')->getAll();
        while( !$stateIt->end() ) {
            $options[$stateIt->getId()] = $stateIt->getDisplayName();
            $stateIt->moveNext();
        }
        $options['order'] = text(2321);
        return $options;
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
