<?php

class FormTransitionPredicateEmbedded extends PMFormEmbedded
{
 	private $entity = null;
    private $transitionIt = null;
 	
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'Predicate':
 				return true;

 			default:
 				return false;
 		}
 	}
 	
 	function getNoItemsMessage() {
 		return text(1141);
 	}
 	
 	function setEntity( $entity ) {
 		$this->entity = $entity;
 	}

 	function setTransitionIt( $it ) {
 	    $this->transitionIt = $it;
    }
 	
	function createField( $attr_name ) 
	{
		switch( $attr_name )
		{
			case 'Predicate':
				$object = getFactory()->getObject('StateBusinessRule');
				$object->addFilter( new StateBusinessEntityFilter($this->entity) );
				return new FieldDictionary( $object	);

			default:
				return parent::createField( $attr_name );
		}
	}

	function drawAddButton($view, $tabindex)
    {
        parent::drawAddButton($view, $tabindex);

        echo '<br/>';
        echo '<br/>';
        echo $view->render('pm/ConditionsLogic.php', array (
            'field' => 'PredicatesLogic',
            'value' => is_object($this->transitionIt) ? $this->transitionIt->get('PredicatesLogic') : '',
            'default' => 'all'
        ));
    }
}