<?php
include_once "FormStateActionEmbedded.php";

class FieldStateAction extends FieldForm
{
 	var $object_it, $object;
 	
 	function FieldStateAction ( $object_it )
 	{
 		$this->object_it = $object_it;
 	}
 	
 	function setObject( $object )
 	{
 		$this->object = $object;
 	}
 	
 	function render( $view )
 	{
 	    $this->draw( $view );
 	}
 	
 	function draw( $view = null )
 	{
 		global $model_factory;

		$anchor = $model_factory->getObject('StateAction');
		if ( is_object($this->object_it) )
		{
			$anchor->addFilter( new FilterAttributePredicate('State', $this->object_it->getId()) );
			$entity = $model_factory->getObject($this->object_it->get('ObjectClass'));
		}
		else
		{
			$anchor->addFilter( new FilterAttributePredicate('State', 0) );
			$entity = $this->object;
		}

 		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';

 			$form = new FormStateActionEmbedded( $anchor, 'State' );
 			
 			$form->setEntity( $entity ); 
	 		$form->setReadonly( $this->readOnly() );
	 			
	 		$form->draw( $view );
 		echo '</div>';
 	}
}