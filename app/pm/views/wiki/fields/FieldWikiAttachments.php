<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/FieldAttachments.php';
include "FormWikiAttachmentEmbedded.php";

class FieldWikiAttachments extends FieldAttachments
{
 	var $form;
 	
	function getForm()
	{
		global $model_factory;

		if ( is_object($this->form) )
		{
			return $this->form;
		}
		
		$files = $model_factory->getObject('WikiPageFile');
		
		$object_it = $this->getObjectIt();
		
		if ( $object_it->getId() > 0 )
		{
			$files->addFilter( new FilterAttributePredicate('WikiPage', $object_it->getId()) );	
		}
		else
		{
			$files->addFilter( new FilterAttributePredicate('WikiPage', 0) );	
		}
		 
 		$this->form = new FormWikiAttachmentEmbedded( $files, 'WikiPage' );
 		
 		$this->form->setAnchorIt( $object_it );
 		
 		$this->form->setReadonly( $this->readOnly() );
 		
 		if ( !$this->getEditMode() ) $this->form->setObjectIt( $object_it );
 		
 		return $this->form;
	}
}