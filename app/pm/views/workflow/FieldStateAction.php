<?php
include_once "FormStateActionEmbedded.php";

class FieldStateAction extends FieldForm
{
 	private $object_it, $object;
 	
 	function __construct ( $object_it ) {
 		$this->object_it = $object_it;
 	}
 	
 	function setObject( $object ) {
 		$this->object = $object;
 	}
 	
 	function render( $view ) {
 	    $this->draw( $view );
 	}
 	
 	function draw( $view = null )
 	{
		$anchor = getFactory()->getObject('StateAction');
		if ( is_object($this->object_it) ) {
            $anchorIt = $anchor->getRegistry()->Query(
                array(
                    new FilterAttributePredicate('State', $this->object_it->getId())
                )
            );
			$entity = getFactory()->getObject($this->object_it->get('ObjectClass'));
		}
		else {
            $anchorIt = $anchor->getEmptyIterator();
			$entity = $this->object;
		}

 		echo '<div class="'.(!$this->readOnly() ? "attwritable" : "attreadonly").'">';
 			$form = new FormStateActionEmbedded( $anchorIt, 'State' );
 			$form->setEntity( $entity );
	 		$form->setReadonly( $this->readOnly() );
	 		$form->draw( $view );
 		echo '</div>';
 	}
}