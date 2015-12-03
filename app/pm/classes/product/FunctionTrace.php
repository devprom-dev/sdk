<?php

include 'FunctionTraceIterator.php';
include 'FunctionInversedTraceIterator.php';

include 'predicates/FunctionTraceClassPredicate.php';
include 'predicates/FunctionTraceObjectPredicate.php';

class FunctionTrace extends Metaobject
{
 	function FunctionTrace() 
 	{
 		parent::Metaobject('pm_FunctionTrace');
 		
 		$object_class = $this->getObjectClass();
 		
 		if ( $object_class != '' )
 		{
 		    $this->setAttributeType('ObjectId', 'REF_'.$object_class.'Id');
 		    
 		    $this->setAttributeRequired('ObjectId', true);
 		}
 	}
 	
 	function createIterator() 
 	{
 		return new FunctionTraceIterator( $this );
 	}

	function getBaselineReference() {
		return 'Baseline';
	}

 	function getObjectClass()
 	{
 		return '';
 	}

	function getObjectIt( $function_it )
	{
		global $model_factory;
		
		$it = $this->getByRefArray( array( 
			'Feature' => $request_it->getId() 
		));

		$object = $model_factory->getObject( $this->getObjectClass() );
		
        if( $it->count() < 1 ) return $object->getEmptyIterator();
		
		return $object->getExact( $it->fieldToArray('ObjectId') );
	}

	function getFunctionIt( $object_it )
	{
		global $model_factory;
		
		$it = $this->getByRef('ObjectId', $object_it->getId());
		
		$request = $model_factory->getObject('pm_Function');
		
        if( $it->count() < 1 ) return $request->getEmptyIterator();
		
		return $request->getExact( $it->fieldToArray('Feature') );
	}

 	function resetFilters()
 	{
 		parent::resetFilters();
 		
 		if ( $this->getObjectClass() == '' ) return;
 		
	 	$this->addFilter( new FunctionTraceClassPredicate( $this->getObjectClass() ) );
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