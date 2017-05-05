<?php
define( 'REGEX_SHRINK', '/(^|[^=]"|[^="])((http:|https:)\/\/([\w\.\/:\-\?\%\=\#\&\;\+\,\(\)\[\]_]+[_\w\.\/:\-\?\%\=\#\&\;\+\,]{1}))/im');

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
	private $search_text = array();
 	
 	function FieldWYSIWYG( $editor_class = '' )
 	{
 		parent::Field();
 		
 		$this->show_attachments = true;
 		
 		$this->edit_mode = true;
 		
 		$this->editor_mode_default = WIKI_MODE_MINIMAL | WIKI_MODE_INLINE; 
 		
 		$this->editor = WikiEditorBuilder::build($editor_class);
 		
		$this->editor->setMode( $this->editor_mode_default );
 	}

	function setSearchText($text) {
		$this->search_text = SearchRules::getSearchItems($text, getSession()->getLanguageUid());
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

	function setToolbar( $mode ) {
		$this->editor->setToolbar($mode);
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

	function getValue()
	{
		$value = parent::getValue();
		if ( count($this->search_text) > 0 ) {
			$value = preg_replace(
				array_map(
					function($value) {
						return '#'.$value.'#iu';
					},
					$this->search_text
				),
				'<span class="label label-found">\\0</span>',
				$value
			);
		}
		return $value;
	}

	function getText( $readonly = false )
	{
		$editor = $this->getEditor();

		if ( $editor->getMode() & WIKI_MODE_INPLACE_INPUT ) {
			return parent::getText();
		}
		
		$parser = $readonly ? $editor->getHtmlParser() : $editor->getPageParser();
		$parser->displayHints(true);
		$parser->setObjectIt( $this->object_it );

		$content = $parser->parse(html_entity_decode($this->getValue(), ENT_QUOTES | ENT_HTML401, APP_ENCODING));
		if ( $readonly ) {
			$content = preg_replace_callback(REGEX_SHRINK, array($this, 'shrinkLongUrl'), $content);
			$content = TextUtils::breakLongWords($content);
		}
		return TextUtils::getValidHtml($content);
	}
	
	function drawReadonly()
	{
		echo '<div id="'.$this->getId().'" class="reset '.($this->getMode() & WIKI_MODE_INPLACE_INPUT ? 'wysiwyg-input' : 'wysiwyg').'" attributename="'.$this->getName().'" name="'.$this->getName().'">';
		    echo $this->getText(true);
		echo '</div>';
	}
	
	function draw( $view = null )
	{
	    $value = $this->getValue();
		if ( $this->readOnly() || !$this->getEditMode() && (preg_match(REGEX_INCLUDE_PAGE, $value) || preg_match(REGEX_INCLUDE_REVISION, $value)) )
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

	function shrinkLongUrl( $match )
	{
		$context = $match[1].$match[5];
		if ( $context == '=""' || $context == '="">' ) return $match[0];

		$display_name = trim($match[2], "\.\,\;\:");

		$shrink_length = 80;
		if ( strlen($display_name) > $shrink_length )
		{
			$display_name = substr($display_name, 0, $shrink_length/2).'[...]'.
				substr($display_name, strlen($display_name) - $shrink_length/2, $shrink_length/2);
		}
		return $match[1].$display_name.$match[5];
	}
}