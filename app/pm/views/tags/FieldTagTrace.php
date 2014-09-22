<?php

include_once "TagFormEmbedded.php";

class FieldTagTrace extends FieldForm
{
 	var $anchor = null;
 	var $field = '';
 	
 	function __construct( $anchor, $field )
 	{
 		$this->anchor = $anchor;
 		$this->field = $field;
 	}

 	function draw()
	{
		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
			$this->drawBody();
		echo '</div>';
	}
	
	function getField()
	{
	    return $this->field;    
	}
	
	function getAnchorIt()
	{
		return $this->anchor;
	}
	
	function getTagObject()
	{
		return null;
	}
	
	function getForm()
	{
	    return new TagFormEmbedded( $this->getTagObject(), $this->getField() );
	}
	
 	function render( & $view )
	{
	    $this->drawBody( $view );    
	}
	
	function drawBody( & $view = null )
	{
		global $model_factory;
		
		$tag = $this->getTagObject();
		
 		$form = $this->getForm();
 			
 		if ( is_object($this->anchor) && !$this->getEditMode() ) $form->setObjectIt( $this->anchor );

 		$form->setReadonly( $this->readOnly() );
 			
 		$form->setTabIndex( $this->getTabIndex() );
 		
 		echo '<div>';
	 		$form->draw( $view );
 		echo '</div>';
	}
}