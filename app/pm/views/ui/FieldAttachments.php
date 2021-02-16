<?php

include_once "FormAttachmentEmbedded.php";

class FieldAttachments extends FieldForm
{
 	var $object_it, $attachments, $caution, $anchor_field;
	private $object = null;
	private $addButtonText = '';
	private $appendButtonTemplate = 'pm/AttachmentsAppendButton.php';
 	
 	function FieldAttachments( $object_it = null, $writable = true, $caution = true )
 	{
 		if ( is_a($object_it, 'IteratorBase') )
 		{
 		    $this->object_it = $object_it;
			$this->object = $object_it->object;
 		}
 		else
 		{
			$this->object = $object_it;
 		    $this->object_it = $object_it->getEmptyIterator();
 		}
 		
 		// reports the caution message that form values may be lost
 		$this->caution = $caution;
 		$this->addButtonText = translate('добавить');
 		$this->anchor_field = 'ObjectId';
 	}

 	function setButtonTemplate( $templatePath ) {
 	    $this->appendButtonTemplate = $templatePath;
    }

 	function getAttachmentIt()
 	{
 		if ( is_object($this->object_it) ) {
			$this->attachments = getFactory()->getObject('pm_Attachment');
			$this->attachments->addFilter( new AttachmentObjectPredicate($this->object_it) );
            $this->attachments->disableVpd();
            return $this->attachments->getAll();
 		}
 		else {
 		    return getFactory()->getObject('pm_Attachment')->getEmptyIterator();
 		}
 	}
 	
	function setAddButtonText( $text ) {
		$this->addButtonText = $text;
	}
	
 	function getObjectIt() {
 		return $this->object_it;
 	}

	function getObject() {
		return $this->object;
	}
 	
 	function draw( $view = null )
	{
		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
			$this->render($view);
		echo '</div>';
	}
	
 	function render( $view )
	{
        echo '<div class="attachment-items">';
	        $attachmentIt = $this->getAttachmentIt();
            while( !$attachmentIt->end() )
            {
                $actions = array();

                if ( !$this->readOnly() && getFactory()->getAccessPolicy()->can_delete($attachmentIt) ) {
                    if ( $this->getEditMode() ) {
                        $actions['delete'] = array(
                            'name' => translate('Удалить'),
                            'uid' => 'delete',
                            'url' => "javascript: filesFormDelete(".$attachmentIt->getId().", '".get_class($attachmentIt->object)."');"
                        );
                    }
                    else {
                        $method = new DeleteObjectWebMethod($attachmentIt);
                        $actions['delete'] = array(
                            'name' => translate('Удалить'),
                            'uid' => 'delete',
                            'url' => $method->getJSCall()
                        );
                    }
                }

                echo $view->render('core/EmbeddedRowAttachmentMenu.php', array (
                    'title' => $attachmentIt->getFileLink(),
                    'info' => $attachmentIt->getFileInfo(),
                    'id' => $attachmentIt->getId(),
                    'class' => get_class($attachmentIt->object),
                    'items' => $actions,
                    'position' => 'last'
                ));

                $attachmentIt->moveNext();
            }
         echo '</div>';

        if ( $this->readOnly() ) return;

        if ( is_object($this->object_it) ) {
            $objectid = $this->object_it->getId();
            $objectclass = get_class($this->object);
        }
        else {
            $objectclass = get_class($this->object);
        }

        echo $view->render($this->appendButtonTemplate, array(
            'title' => $this->addButtonText,
            'action' => $this->getEditMode() ? '' : 'refresh',
            'objectid' => $this->getEditMode() ? '' : $objectid,
            'objectclass' => $objectclass,
            'attachmentClass' => get_class($attachmentIt->object),
            'project' => $this->object_it->get('ProjectCodeName') != ''
                            ? $this->object_it->get('ProjectCodeName')
                            : getSession()->getProjectIt()->get('CodeName'),
            'count' => $attachmentIt->count()
        ));
	}

    function getCssClass() {
        return 'file-drop-target';
    }
}