<?php

include_once "FieldCommentAttachments.php";
 
class CommentForm extends PMForm
{
 	var $control_uid;
 	var $anchor_it;
 	
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
 		$this->control_uid = md5($object_it->object->getClassName().$object_it->getId());
 		
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
 			'&ObjectClass='.get_class($anchor_it->object).'&PrevComment='.SanitizeUrl::parseUrl($_REQUEST['prevcomment']);
 		
 		return 'managecomment'.$parms;
 	}

	function getAttributes()
	{
		return array('Caption', 'Attachments'); 	
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
				return 'files'; 	
				
			default:
				return parent::getAttributeType( $attribute );
		}
	}
	
	function drawCustomAttribute( $attribute, $value, $tab_index )
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

				echo '<div class="uneditable-input" style="width:35%;height:auto;overflow:inherit;">';
					$field->draw($this->getView());
				echo '</div>';

				break;

			default:
				parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}
	
	function getTemplate()
	{
		return "pm/CommentsForm.php";
	}

	function getRenderParms()
	{
		return array_merge(
			parent::getRenderParms(),
			array (
				'fields_separator' => ''
			)
		);
	}
}