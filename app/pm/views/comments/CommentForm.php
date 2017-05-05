<?php

include_once "FieldCommentAttachments.php";
include_once "FieldCheckNotifications.php";
 
class CommentForm extends PMForm
{
 	var $control_uid;
 	var $anchor_it;
 	private $prevCommentIt = null;

 	function __construct($object)
    {
        parent::__construct($object);
        $this->prevCommentIt = $object->getExact($_REQUEST['prevcomment']);
    }

    function setControlUID( $uid )
 	{
 		$this->control_uid = $uid;
 	}
 	
 	function getModifyCaption()
 	{
 		return '';
 	}

 	function getAddCaption()
 	{
 		return '';
 	}
 	
 	function setAnchorIt( $object_it )
 	{
 		$this->control_uid = md5($object_it->object->getClassName().$object_it->getId().$_REQUEST['formonly']);
 		$this->anchor_it = $object_it;
 	}
 	
 	function getAnchorIt()
 	{
 		global $model_factory, $_REQUEST;
 		
 		if ( !is_object($this->anchor_it) )
 		{
 			$anchor = $model_factory->getObject($_REQUEST['objectclass']);
	 		$this->anchor_it = $anchor->getExact($_REQUEST['object']);
 		}
 		
 		return $this->anchor_it;
 	}
 	
 	function getCommandClass()
 	{
 		global $_REQUEST;

 		$anchor_it = $this->getAnchorIt();
 		
 		$parms = '&ObjectId='.$anchor_it->getId().
 			'&ObjectClass='.get_class($anchor_it->object).'&PrevComment='.$this->prevCommentIt->getId();
 		
 		return 'managecomment'.$parms;
 	}

	function getAttributes()
	{
		return array('Caption', 'Attachments', 'Notification');
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			default:
				return ''; 	
		}
	}

	function IsAttributeRequired( $attribute )
	{
		return false; 	
	}

	function IsAttributeVisible( $attribute )
	{
		switch ( $attribute )
		{
			case 'Caption':
			case 'Attachments':
				return true;
            case 'Notification':
                return $this->prevCommentIt->get('IsPrivate') != 'Y';
			default:
				return false; 	
		}
	}

	function getButtonText()
	{
        return translate('Сохранить');
	}

	function getWidth()
	{
		return '100%';
	}

	function IsCentered()
	{
		return false;
	}
	
	function getClass()
	{
		return 'ajax_form_comment';
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Caption':
				return 'wysiwyg'; 	
			case 'Attachments':
            case 'Notification':
				return 'files'; 	
			default:
				return parent::getAttributeType( $attribute );
		}
	}

	function getAttributeValue($attribute)
    {
        switch( $attribute ) {
            case 'Notification':
                if ( $this->prevCommentIt->get('IsPrivate') != 'Y' ) {
                    return parent::getAttributeValue($attribute);
                }
                return 'N';
            default:
                return parent::getAttributeValue($attribute);
        }
    }

    function drawCustomAttribute( $attribute, $value, $tab_index, $view )
	{
 		$object_it = $this->getObjectIt();
 		
		switch ( $attribute ) 
		{
			case 'Caption':
                $field = new FieldWYSIWYG();
						
 				is_object($object_it) ? 
					$field->setObjectIt( $object_it ) : 
						$field->setObject( $this->getObject() );

				$editor = $field->getEditor();
				
				$editor->setMode( WIKI_MODE_MINIMAL );
						
				$field->setTabIndex($tab_index);
				$field->setValue($value);
				$field->setName($attribute);
				$field->setCssClassName( 'wysiwyg_bottom wysiwyg_right' );
				
				$field->setId('Caption'.$this->control_uid);
				
				$field->draw();
				
				break;
			
			case 'Attachments':
				$field = new FieldCommentAttachments( is_object($object_it) ? $object_it : $this->object );
				$field->setTabIndex($tab_index);
				$field->setName($attribute);
				$field->setReadonly(false);
				$field->setAddButtonText(text(2081));
    			$field->draw($this->getView());
				break;

            case 'Notification':
                $field = new FieldCheckNotifications();
                $field->setAnchor($this->getAnchorIt());
                $field->setTabIndex($tab_index);
                $field->setName($attribute);
                $field->draw($this->getView());
                break;

            default:
				parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
		}
	}

	function getDescription($attribute)
    {
        $description = parent::getDescription($attribute);
        switch( $attribute ) {
            default:
                if ( $description == '' && $this->getObject()->getAttributeType($attribute) == 'wysiwyg' ) {
                    $description = str_replace('%1', getFactory()->getObject('Module')->getExact('dicts-texttemplate')->getUrl(), text(606));
                }
                return $description;
        }
    }

    function getTemplate()
	{
		return "pm/CommentsForm.php";
	}

	function getRenderParms( $view )
	{
		return array_merge(
			parent::getRenderParms($view),
			array (
				'fields_separator' => ''
			)
		);
	}
}