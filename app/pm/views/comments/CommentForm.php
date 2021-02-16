<?php
use Devprom\ProjectBundle\Service\Email\CommentNotificationService;

include_once SERVER_ROOT_PATH."pm/methods/CommentWebMethod.php";
include_once "FieldCommentAttachments.php";
include_once "FieldCheckNotifications.php";
 
class CommentForm extends PMForm
{
 	var $control_uid;
 	var $anchor_it;
 	private $method = null;
 	private $prevCommentIt = null;
 	private $emails = array();

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

        $items = getFactory()->getEventsManager()->getNotificators('ServicedeskCommentEmailNotificator');
        if ( count($items) > 0 && $this->anchor_it->object instanceof Request )
        {
            $notificator = array_shift($items);
            $this->emails = $notificator->getEmails($this->anchor_it);
        }

        $this->method = new CommentWebMethod($this->anchor_it);
 	}
 	
 	function getAnchorIt() {
 		return $this->anchor_it;
 	}
 	
 	function getCommandClass()
 	{
 		$anchor_it = $this->getAnchorIt();
 		
 		$parms = '&ObjectId='.$anchor_it->getId().
 			'&ObjectClass='.get_class($anchor_it->object).'&PrevComment='.$this->prevCommentIt->getId();
 		
 		return 'managecomment'.$parms;
 	}

	function getAttributes() {
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

	function IsAttributeRequired( $attribute ) {
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
                return $_REQUEST['IsPrivate'] != 'Y' && $this->prevCommentIt->get('IsPrivate') != 'Y';
			default:
				return false; 	
		}
	}

	function IsAttributeModifiable($attribute)
    {
        switch( $attribute ) {
            case 'Caption':
                if ( is_object($this->method) ) return $this->method->hasAccess();
                return parent::IsAttributeModifiable($attribute);
            default:
                return parent::IsAttributeModifiable($attribute);
        }
    }

    function getButtonText() {
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
				return 'custom';
			default:
				return parent::getAttributeType( $attribute );
		}
	}

	function getAttributeValue($attribute)
    {
        switch( $attribute ) {
            case 'Notification':
                if ( $this->prevCommentIt->get('IsPrivate') != 'Y' ) {
                    if ( $_REQUEST['IsPrivate'] == 'Y' ) return 'N';
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
                $options = new CommentNotificationService($this->anchor_it);
                $field = new FieldCheckNotifications();
                $field->setEmails($options->getEmails());
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