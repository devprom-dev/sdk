<?php
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';
include_once SERVER_ROOT_PATH."pm/views/ui/FieldEditable.php";

class FieldWYSIWYG extends FieldEditable
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
	    if ( !is_object($this->object_it) ) return parent::getValue();
        $editor = $this->getEditor();
        $parser = $editor->getPageParser();
        $parser->setObjectIt( $this->object_it );

        return htmlspecialchars(
		    $parser->parse(
                html_entity_decode(
                    parent::getValue(),
                    ENT_QUOTES | ENT_HTML401, APP_ENCODING)
            ),
            ENT_QUOTES | ENT_HTML401, APP_ENCODING
        );
	}

	function getText()
	{
        if ( !is_object($this->object_it) ) return parent::getText();

		$editor = $this->getEditor();
		$parser = $editor->getHtmlParser();
		$parser->displayHints(true);
		$parser->setObjectIt( $this->object_it );

		$content = $parser->parse(
            html_entity_decode(
                parent::getValue(), ENT_QUOTES | ENT_HTML401, APP_ENCODING
            )
        );

        if ( count($this->search_text) > 0 ) {
            $content = preg_replace(
                array_map(
                    function($value) {
                        return '#'.$value.'#iu';
                    },
                    $this->search_text
                ),
                '<span class="label label-found">\\0</span>',
                $content
            );
        }

		return $content;
	}
	
	function drawReadonly()
	{
	    if ( is_object($this->object_it) ) {
	        $project = $this->object_it->get('ProjectCodeName');
	        $objectclass = get_class($this->object_it->object);
	        $objectid = $this->object_it->getId();
	        if ( $this->object_it instanceof WikiPageIterator ) {
                $annotation = $this->object_it->getAnnotationData();
            }
        }
		echo '<div id="'.$this->getId().'" class="reset '.($this->getMode() & WIKI_MODE_INPLACE_INPUT ? 'wysiwyg-input' : 'wysiwyg').'" 
		            attributename="'.$this->getName().'" 
		            name="'.$this->getName().'"
		            project="'.$project.'"
		            objectclass="'.$objectclass.'"
		            objectid="'.$objectid.'"
		            annotation="'.htmlentities($annotation).'" >';
		    echo $this->getMode() & WIKI_MODE_INPLACE_INPUT ? $this->getValue() : $this->getText();
		echo '</div>';
	}

	function contentEditable()
    {
        $value = parent::getValue();
        return !preg_match(REGEX_INCLUDE_PAGE, $value)
            && !preg_match(REGEX_INCLUDE_REVISION, $value);
    }

	function draw( $view = null )
	{
	    if ( $this->readOnly() ) {
            $this->drawReadonly();
            return;
        }

		if ( !$this->getEditMode() && !$this->contentEditable() ) {
			$this->drawReadonly();
			return;
		}
		
		$editor = $this->getEditor();
		
		if ( $this->show_attachments )
		{
			if ( !is_object($this->object_it) ) {
				$object = $editor->getObject();
				$this->object_it = $object->getEmptyIterator();
			}
		}
		
		$editor->setFieldId( $this->getId().abs(crc32(microtime())) );
		$editor->setFieldName( $this->getName() );
		$editor->setRequired( $this->getRequired() );

		$editor->draw( $this->getValue(), $this->getEditMode() );
	}
	
	function drawScripts() {
	    $editor = $this->getEditor();
	    $editor->drawScripts();
	}
}