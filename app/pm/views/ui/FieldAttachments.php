<?php

include_once "FormAttachmentEmbedded.php";

class FieldAttachments extends FieldForm
{
 	var $object_it, $attachments, $caution, $anchor_field;
 	private $image_class = 'image_attach';
 	
 	function FieldAttachments( $object_it = null, $writable = true, $caution = true )
 	{
 		global $model_factory;
 		
 		if ( is_a($object_it, 'IteratorBase') )
 		{
 		    $this->object_it = $object_it;
 		}
 		else
 		{
 		    $this->object_it = $object_it->createCachedIterator(array());
 		}
 		
 		// reports the caution message that form values may be lost
 		$this->caution = $caution;
 		
 		$this->anchor_field = 'ObjectId';
 	}
 	
 	function getAttachments()
 	{
 	    global $model_factory;
 	    
 	    if ( is_object($this->attachments) ) return $this->attachments;
 	    
 		if ( is_object($this->object_it) )
 		{
			$this->attachments = $model_factory->getObject2('pm_Attachment', $this->object_it->count());

			$this->attachments->addFilter( new AttachmentObjectPredicate($this->object_it) );

 		    $this->attachments->setVpdContext($this->object_it);

 		}
 		else
 		{
 		    $this->attachments = $model_factory->getObject('pm_Attachment');
 		    	
 		    $this->attachments->addFilter( new AttachmentObjectPredicate(0) );
 		}
 		
 		return $this->attachments;
 	}
 	
 	function setAttachments( $attachments )
 	{
 		$this->attachments = $attachments;
 	}

 	function setImageClass( $class_name )
 	{
 		$this->image_class = $class_name;
 	}
 	
 	function getObjectIt()
 	{
 		return $this->object_it;
 	}
 	
 	function draw()
	{
		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
			$this->drawBody();
		echo '</div>';
	}
	
	function getForm()
	{
 		$form = new FormAttachmentEmbedded( $this->getAttachments(), $this->anchor_field );
 		
 		$form->setImageClass( $this->image_class );
 		$form->setAnchorIt( $this->getObjectIt() );

 		return $form;
	}
	
 	function render( $view )
	{
	    $this->drawBody( $view );    
	}
	
	function drawBody( $view = null )
	{
		global $model_factory;
		
		$object_it = $this->getObjectIt();
		
		$form = $this->getForm();
		
 		if ( is_object($object_it) )
 		{
 			if ( !$this->getEditMode() ) $form->setObjectIt( $object_it );
 		}

 		$form->setReadonly( $this->readOnly() );
 			
 		$form->setTabIndex( $this->getTabIndex() );
 		
 		$form->setFormId( 1000 + $form->getFormId() );

 		$form->draw( $view );
	}
}