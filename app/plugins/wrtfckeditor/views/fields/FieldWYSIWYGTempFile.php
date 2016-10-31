<?php
include_once SERVER_ROOT_PATH . "cms/views/Field.php";

class FieldWYSIWYGTempFile extends Field
{
 	private $form;
 	
 	function getForm()
 	{
		if ( is_object($this->form) ) return $this->form;

		$attachments = getFactory()->getObject('cms_TempFile' );
		$attachments->addFilter( new FilterAttributePredicate('Caption', 'none') );
			
 		$this->form = new FormAttachmentEmbedded( $attachments, 'Caption' );
 		$this->form->setFormId( 1000 + $this->form->getFormId() );
 		$this->form->setImageClass('modify_image');

 		return $this->form;
 	}
 	
 	function draw( $view = null ) {
		$this->drawBody();
	}
 	
	function drawBody() {
		$this->getForm()->draw();
	}
} 