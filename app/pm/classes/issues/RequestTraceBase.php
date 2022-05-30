<?php

include "RequestTraceBaseRegistry.php";
include "RequestTraceBaseIterator.php";
include "RequestInversedTraceBaseIterator.php";
include "predicates/RequestTraceObjectPredicate.php";
include "predicates/RequestTracePredicate.php";
include "predicates/RequestTraceWikiPredicate.php";
include "predicates/RequestTraceStatePredicate.php";
include "predicates/RequestTraceRequirementLinkedPredicate.php";
     
class RequestTraceBase extends Metaobject
{
 	function __construct( ObjectRegistry $registry = null )
 	{
 		parent::__construct('pm_ChangeRequestTrace', is_object($registry) ? $registry : new RequestTraceBaseRegistry());

        foreach( array('ObjectId','ObjectClass','ChangeRequest') as $attribute ) {
            $this->addAttributeGroup($attribute, 'alternative-key');
        }

 		$object_class = $this->getObjectClass();
 		if ( $object_class != '' ) {
 		    $this->setAttributeType('ObjectId', 'REF_'.$object_class.'Id');
 		}
 		$this->setAttributeRequired('OrderNum', false);
 	}
 	
 	function createIterator() {
 		return new RequestTraceBaseIterator( $this );
 	}

	function getBaselineReference() {
		return 'Baseline';
	}

	function getObjectClass() {
 		return '';
 	}

	function getObjectIt( $request_it )
	{
		$it = $this->getByRefArray(
			array( 'ChangeRequest' => $request_it->getId() ) 
			);

		$object = getFactory()->getObject( $this->getObjectClass() );
		if ( $it->count() < 1 ) return $object->getEmptyIterator();
		
		return $object->getExact( $it->fieldToArray('ObjectId') );
	}

	function getDefaultAttributeValue( $attr )
	{
 		switch ( $attr )
 		{
 			case 'ObjectClass':
 				return $this->getObjectClass();
 				
 			default:
 				return parent::getDefaultAttributeValue( $attr ); 
 		}
	}

    function IsDeletedCascade( $object )
    {
        if ( is_a($object, 'WikiPageChange') ) return false;
        return parent::IsDeletedCascade($object);
    }
}