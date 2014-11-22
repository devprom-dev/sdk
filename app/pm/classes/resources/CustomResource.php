<?php

include_once SERVER_ROOT_PATH."core/classes/Resource.php";

include "CustomResourceIterator.php";
include "CustomResourceRegistry.php";
include "predicates/LangResourceTermPredicate.php";	
include "predicates/LangResourceOverridenPredicate.php";

class CustomResource extends Resource
{
 	var $cache_enabled = true;
 	
 	function __construct()
 	{
 		parent::__construct( new CustomResourceRegistry($this) );
 	}
 	
 	function createIterator()
 	{
 	    return new CustomResourceIterator($this);
 	}

 	function getDisplayName()
 	{
 		return translate('Терминология');
 	}
 	
 	function getCacheEnabled()
 	{
 		return $this->cache_enabled;
 	}
 	
 	function getAll()
 	{
 		$predicate = $this->getFilterPredicate();
 		
 		$items = array();
 		
 		$predicates = preg_split('/and/', $predicate);
 		
 		foreach( $predicates as $predicate )
 		{
 			$parts = preg_split('/=/', $predicate);
 			
 			if ( trim($parts[0]) != '' ) $items = array_merge( $items, array( trim($parts[0]) => trim($parts[1])) );
 		}
 		
 		$this->resetFilters();

 		$it = parent::getAll();

 		if ( count($items) < 1 ) return $it;
 		
 		$data = $it->getRowset();

 		// filter data using given predicated
 		foreach( $data as $key => $resource )
 		{
 			$overriden = $resource['ResourceValue'] != $resource['OriginalValue'];
 				 
 			if ( $items['overriden'] == 'no' && $overriden )
 			{
 			    unset($data[$key]);
 			}
 			
 			if ( $items['overriden'] == 'yes' && !$overriden )
 			{
 			    unset($data[$key]);
 			}
 			
     		if ( $items['contains'] != '' )
     		{
     		    $right = IteratorBase::wintoutf8($items['contains']);
     		    
     		    $value = IteratorBase::wintoutf8($resource['ResourceValue']);
     		    
     		    $original = IteratorBase::wintoutf8($resource['OriginalValue']);
     		    
     		    $remove = mb_stripos($value, $right, 0, 'utf-8') === false 
     		        && mb_stripos($original, $right, 0, 'utf-8') === false;
     		    
     			if ( $remove )
     			{
 			        unset($data[$key]);
     			}
     		}
 		}

 		return $this->createCachedIterator( array_values($data) );
 	}
 	
 	function modify_parms( $object_id, $parms )
 	{
 	    $this->cache_enabled = false;
 	    
 		$object_it = $this->getRegistry()->Query(
 				array( 
 						new FilterAttributePredicate('ResourceKey', $object_id ),
 						new FilterBaseVpdPredicate()
 				)
 		);

 		if ( $object_it->getId() == '' )
 		{    
 			if ( $parms['ResourceValue'] != '' )
 			{
	 			$result = $this->add_parms( 
	 					array( 
					        'ResourceKey' => $object_id,
							'ResourceValue' => $parms['ResourceValue']
	 					)
	 			);
 			}
 		}
 		else
 		{
 			if ( $parms['ResourceValue'] == '' )
 			{
 				$result = $this->delete( $object_it->getId() );
 			}
 			else
 			{
 				$result = parent::modify_parms( $object_it->getId(), $parms );
 			}
 		}
 		
 		$this->cache_enabled = true;
 		
 		return $result;
 	}
}