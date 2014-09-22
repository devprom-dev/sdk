<?php

include "FormLinkedEmbedded.php";

class FieldLinkedRequest extends FieldForm
{
 	var $object_it;
 	
 	function FieldLinkedRequest( $object_it )
 	{
 		$this->object_it = $object_it;
 	}
 	
 	function render( & $view )
 	{
 	    $this->draw( $view );
 	}
 	
 	function draw( & $view = null )
 	{
 		$link = getFactory()->getObject('pm_ChangeRequestLink');
 		
 		$link->disableVpd();
 		
 		$link->addFilter( 
 				new RequestLinkedFilter( is_object($this->object_it) ? $this->object_it->getId() : 0) 
		);
 		
 		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';

	 		$form = new FormLinkedEmbedded( $link, 'SourceRequest' );
	 		
 			$form->setAnchorIt( $this->object_it );
	 		
	 		if ( !$this->getEditMode() ) $form->setObjectIt( $this->object_it );
	 			
	 		$form->setReadonly( $this->readOnly() );
	 		
	 		$form->setTabIndex( $this->getTabIndex() );
	 		
	 		$form->draw( $view );
 		
 		echo '</div>';
 	}
}