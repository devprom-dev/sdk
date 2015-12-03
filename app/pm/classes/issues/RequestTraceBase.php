<?php

include "RequestTraceBaseRegistry.php";
include "RequestTraceBaseIterator.php";
include "RequestInversedTraceBaseIterator.php";

include "predicates/RequestTraceObjectPredicate.php";
include "predicates/RequestTracePredicate.php";
     
class RequestTraceBase extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_ChangeRequestTrace', new RequestTraceBaseRegistry());
 		
 		$object_class = $this->getObjectClass();
 		
 		if ( $object_class != '' )
 		{
 		    $this->setAttributeType('ObjectId', 'REF_'.$object_class.'Id');
 		}
 	}
 	
 	function createIterator() 
 	{
 		return new RequestTraceBaseIterator( $this );
 	}

	function getBaselineReference() {
		return 'Baseline';
	}

	function getObjectClass()
 	{
 		return '';
 	}

	function getObjectIt( $request_it )
	{
		global $model_factory;
		
		$it = $this->getByRefArray(
			array( 'ChangeRequest' => $request_it->getId() ) 
			);

		$object = $model_factory->getObject( $this->getObjectClass() );
		
		if ( $it->count() < 1 ) return $object->getEmptyIterator(); 
		
		return $object->getExact( $it->fieldToArray('ObjectId') );
	}

	function getRequestIt( $object_it )
	{
		global $model_factory;
		
		$it = $this->getByRef('ObjectId', $object_it->getId());
		
		$request = $model_factory->getObject('pm_ChangeRequest');
		
		if ( $it->count() < 1 ) return $request->getEmptyIterator();
		
		return $request->getExact( $it->fieldToArray('ChangeRequest') );
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
}