<?php
include "ComponentTraceIterator.php";
include "ComponentTraceRegistry.php";
include "predicates/ComponentTraceObjectPredicate.php";

class ComponentTrace extends Metaobject
{
 	function __construct()
 	{
 		parent::__construct('pm_ComponentTrace', new ComponentTraceRegistry($this));

        foreach( array('ObjectId','ObjectClass','Component') as $attribute ) {
            $this->addAttributeGroup($attribute, 'alternative-key');
        }
 		$object_class = $this->getObjectClass();
 		if ( $object_class != '' ) {
     		$this->setAttributeType('ObjectId', 'REF_'.$object_class.'Id');
     		$this->setAttributeRequired('ObjectId', true);
 		}
        $this->setAttributeRequired('OrderNum', false);
 	}
 	
 	function createIterator() {
 		return new ComponentTraceIterator( $this );
 	}

 	function getObjectClass() {
 		return '';
 	}

    function getBaselineReference() {
         return '';
    }

	function getDefaultAttributeValue( $attr )
	{
 		switch ( $attr ) {
 			case 'ObjectClass':
 				return $this->getObjectClass();
 			default:
 				return parent::getDefaultAttributeValue( $attr ); 
 		}
	}
}