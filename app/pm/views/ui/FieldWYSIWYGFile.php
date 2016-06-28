<?php

include_once "FieldAttachments.php";

class FieldWYSIWYGFile extends Field
{
 	var $object_it, $form;
 	
 	function __construct( $object_it ) {
 		$this->object_it = $object_it;
 	}
 	
 	function getForm()
 	{
		if ( is_object($this->form) ) return $this->form;

		$attachments = getFactory()->getObject('pm_Attachment' );
		$attachments->addFilter( new AttachmentObjectPredicate($this->object_it) );
			
 		$this->form = new FormAttachmentEmbedded( $attachments, 'ObjectId' );
 		$this->form->setFormId( 1000 + $this->form->getFormId() );
 		$this->form->setImageClass('modify_image');
		$this->form->setAnchorIt( $this->object_it );
		if ( !$this->getEditMode() ) $this->form->setObjectIt( $this->object_it );

 		return $this->form;
 	}
 	
 	function draw( $view = null ) {
		$this->drawBody();
	}
 	
	function drawBody() {
		$this->getForm()->draw();
	}
} 