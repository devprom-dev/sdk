<?php
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';
include_once SERVER_ROOT_PATH."pm/views/ui/FieldEditable.php";

class FieldTextEditable extends FieldEditable
{
 	var $object_it;
 	var $editor;

 	function __construct( $editor_class = '' )
 	{
 		parent::__construct();
 		$this->editor = WikiEditorBuilder::build($editor_class);
 	}

 	function setObjectIt( $object_it )
 	{
 	    if ( is_object($object_it) ) {
            $this->object_it = $object_it->copy();
        }
 	    else {
            $this->object_it = getSession()->getProjectIt();
        }
        $this->editor->setObjectIt($this->object_it);
 	}
 	
 	function setObject( $object ) {
 		$this->editor->setObject( $object );
 	}
 	
 	function setTabIndex( $index ) {
 		$this->editor->setTabIndex( $index );
 	}
 	
 	function setRows( $rows ) {
 		$this->editor->setMinRows( $rows );
 	}
 	
	function drawReadonly()
	{
		echo '<div id="'.$this->getId().'" class="reset wysiwyg-input" attributename="'.$this->getName().'" name="'.$this->getName().'">';
		    echo $this->getValue();
		echo '</div>';
	}
	
	function draw( $view = null )
	{
	    if ( $this->readOnly() ) {
            $this->drawReadonly();
            return;
        }

		$this->editor->setFieldId( $this->getId().abs(crc32(microtime())) );
        $this->editor->setFieldName( $this->getName() );
        $this->editor->setRequired( $this->getRequired() );
        $this->editor->setMode( WIKI_MODE_INPLACE_INPUT );
        $this->editor->draw( $this->getValue(), false );
	}
	
	function drawScripts() {
        $this->editor->drawScripts();
	}
}