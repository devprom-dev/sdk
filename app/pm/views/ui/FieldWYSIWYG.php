<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

include "FieldWYSIWYGFile.php";

class FieldWYSIWYG extends Field
{
 	var $object_it;
 	var $editor;
 	var $show_attachments;
 	var $attachments_field;
 	var $edit_mode;
 	var $editor_mode_default;
 	var $has_border = true;
 	
 	function FieldWYSIWYG( $editor_class = '' )
 	{
 		parent::Field();
 		
 		$this->show_attachments = true;
 		
 		$this->edit_mode = true;
 		
 		$this->editor_mode_default = WIKI_MODE_MINIMAL | WIKI_MODE_INLINE; 
 		
 		$this->editor = WikiEditorBuilder::build($editor_class);
 		
		$this->editor->setMode( $this->editor_mode_default );
 	}
 	
 	function showAttachments( $visible )
 	{
 		$this->show_attachments = $visible;
 	}
 	
 	function setObjectIt( $object_it )
 	{
 		$this->object_it = $object_it->copy();
 		
 		$editor = $this->getEditor();
 		$editor->setObjectIt($this->object_it);
 	}
 	
 	function setObject( $object )
 	{
 		$this->editor->setObject( $object );
 	}
 	
 	function setTabIndex( $index )
 	{
 		$this->editor->setTabIndex( $index );
 	}
 	
 	function setCSSClassName( $class_name )
 	{
 	    $this->editor->setCSSClassName( $class_name );
 	}
 	
 	function setRows( $rows )
 	{
 		$this->editor->setMinRows( $rows );
 	}
 	
	function & getEditor()
	{
		return $this->editor;
	}
	
	function setMode( $mode )
	{
	    $this->editor_mode_default = $mode;
	}
	
	function getMode()
	{
	    return $this->editor->getMode();
	}
	
	function setAttachmentsField( $field)
	{
		$this->attachments_field = $field;
	}
	
	function setHasBorder( $border )
	{
		$this->has_border = $border;
	}
	
	function hasBorder()
	{
		return $this->has_border;
	}
	
	function getText()
	{
		$editor = $this->getEditor();
		
		$parser = $editor->getPageParser();
		$parser->displayHints(true);
		$parser->setObjectIt( $this->object_it );
		
		return $parser->parse( html_entity_decode($this->getValue(), ENT_QUOTES | ENT_HTML401, APP_ENCODING) );
	}
	
	function drawReadonly()
	{
		$editor = $this->getEditor();
	    
		echo '<div class="reset '.($this->getMode() & WIKI_MODE_INPLACE_INPUT ? 'wysiwyg-input' : 'wysiwyg').'" attributename="'.$this->getName().'">';
		    echo $this->getText();
		echo '</div>';
	}
	
	function draw()
	{
		if ( $this->readOnly() || !$this->getEditMode() && preg_match(REGEX_INCLUDE_PAGE, $this->getValue()) )
		{
			$this->drawReadonly();
			
			return;
		}
		
		$editor = $this->getEditor();
		
		if ( $this->show_attachments )
		{
			if ( !is_object($this->object_it) )
			{
				$object = $editor->getObject();
				
				$this->object_it = $object->getEmptyIterator();
			}
			
			$field = is_object($this->attachments_field)
				? $this->attachments_field : new FieldWYSIWYGFile( $this->object_it );

			$field->setEditMode( $this->getEditMode() );
			
			$editor->setAttachmentsField( $field );
		}
		
		$editor->setFieldId( $this->getId().abs(crc32(microtime())) );
		
		$editor->setFieldName( $this->getName() );
		
		$editor->setRequired( $this->getRequired() );
		
		$editor->draw( $this->getEditMode() ? $this->getValue() : $this->getText(), $this->getEditMode() );
	}
	
	function drawScripts()
	{
	    $editor = $this->getEditor();

	    $editor->drawScripts();
	}
	
}