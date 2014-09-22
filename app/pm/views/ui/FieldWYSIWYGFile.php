<?php

include_once "FieldAttachments.php";

class FieldWYSIWYGFile extends Field
{
 	var $object_it, $form;
 	
 	function FieldWYSIWYGFile( $object_it )
 	{
 		$this->object_it = $object_it;
 	}
 	
 	function getForm()
 	{
		global $model_factory;
		
		if ( is_object($this->form) )
		{
			return $this->form;
		}
		
		$attachments = $model_factory->getObject('pm_Attachment' );
		
		$attachments->addFilter( new AttachmentObjectPredicate($this->object_it) );
			
 		$this->form = new FormAttachmentEmbedded( $attachments, 'ObjectId' );
 		
 		$this->form->setAnchorIt( $this->object_it );

 		if ( !$this->getEditMode() ) $this->form->setObjectIt( $this->object_it );
 		
 		$this->form->setFormId( 1000 + $this->form->getFormId() );
 		
 		return $this->form;
 	}
 	
 	function draw()
	{
		$this->drawBody();
	}
 	
	function drawBody()
	{
		$form = $this->getForm();
		
 		$form->draw();
	}
} 