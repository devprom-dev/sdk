<?php
include "StateAttributeIterator.php";
include "predicates/TransitionAttributeEntityAttributesPredicate.php";

class StateAttribute extends MetaobjectCacheable
{
    function __construct() 
 	{
 		parent::__construct('pm_StateAttribute');
 		$this->setAttributeType('Entity', 'REF_ConfigurableObjectId');
        foreach( array('State', 'Transition') as $attribute ) {
            $this->setAttributeEditable($attribute, false);
        }
 		$this->setSortDefault( array(
 		    new SortAttributeClause('State'),
            new SortAttributeClause('Transition')
        ));
        $this->setAttributeEditable('OrderNum', false);
        $this->addAttributeGroup('OrderNum', 'system');
 	}
 	
 	function createIterator() {
 		return new StateAttributeIterator( $this );
 	}
 	
	function getDefaultAttributeValue( $name )
	{
		switch ( $name ) {
			case 'Entity':
				return $this->getAttributeObject('State')->getObjectClass();
			default:
				return parent::getDefaultAttributeValue( $name );
		}
	}

	function getDisplayName() {
        return text(3200);
    }
}