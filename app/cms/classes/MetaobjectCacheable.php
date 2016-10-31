<?php
 
include_once "Metaobject.php";

class MetaobjectCacheable extends Metaobject
{
	private $check_is_cacheable = true;
	private $cache_category = '';
    
	function __construct( $ref_name, ObjectRegistrySQL $registry = null )
	{
	    parent::__construct( $ref_name, $registry );
	    $this->cache_category = getFactory()->getEntityOriginationService()->getCacheCategory($this);
	}
	
    function getCacheCategory()
	{
		return $this->cache_category;
	}
	
	function getCacheKey( $getter, $class_name = '' )
	{
	    if ( $class_name == '' ) $class_name = get_class($this);
	    
		return 'dictionary-'.$getter.'-'.$class_name;
	}

 	function resetCache()
	{
		$getters = array ( GETTER_ALL, GETTER_LATEST, GETTER_FIRST, GETTER_COUNT ); 
		        
		$class_name = get_class($this);
		
		do 
		{
		    foreach( $getters as $getter )
		    {
    		    getFactory()->getCacheService()->set($this->getCacheKey($getter, $class_name), '', $this->getCacheCategory());
		    }
		    
		    $class_name = get_parent_class($class_name);
		} 
		while( $class_name !== false && strtolower($class_name) != 'metaobject');
	}
	
	function getCachedQuery( $getter, $getter_handler )
	{
		$filters = $this->getFilters();
		
		$sort = $this->getSortClause();

		if ( $sort != '' )
		{
			if ( ALLOW_DEBUG )
			{
				getFactory()->debug( 'Skip cache "'.get_class($this).'" ('.$this->getClassName().') on '.$getter.': custom sort' );
			}
		    
			return call_user_func($getter_handler);
		}
		
		if ( count($filters) > 0 )
		{
			if ( ALLOW_DEBUG )
			{
				getFactory()->debug( 'Skip cache "'.get_class($this).'" ('.$this->getClassName().') on '.$getter.': custom filters' );
			}
		    
			return call_user_func($getter_handler);
		}
		
		if ( !$this->check_is_cacheable )
		{
			if ( ALLOW_DEBUG )
			{
				getFactory()->debug( 'Skip cache "'.get_class($this).'" ('.$this->getClassName().') on '.$getter.': cache disabled' );
			}
		    
			return call_user_func($getter_handler);
		}

		$iterator = getFactory()->getCacheService()->get($this->getCacheKey($getter), $this->getCacheCategory());
		
		if ( is_object($iterator) && $iterator->count() > 0 )
		{
			if ( ALLOW_DEBUG )
			{
				getFactory()->debug( 'Hit cache "'.get_class($this).'" ('.$this->getClassName().') on '.$getter.': '.$this->getCacheCategory() );
			}
			
			$iterator->setObject($this);
			
			return $iterator;
		}
		else
		{
			if ( ALLOW_DEBUG )
			{
				getFactory()->debug( 'Missed cache "'.get_class($this).'" ('.$this->getClassName().') on '.$getter.': '.$this->getCacheCategory() );
			}
		    
			$it = call_user_func($getter_handler);
			
			getFactory()->getCacheService()->set( $this->getCacheKey($getter), $it->copyAll(), $this->getCacheCategory() );
			
			return $it;
		}
	}
	 
	function getAll()  
	{
	    $parent_getter = array($this, 'StoredObjectDB::getAll');

	    $iterator = $this->getCachedQuery( GETTER_ALL, function() use($parent_getter) 
	    { 
	        return call_user_func($parent_getter); 
	    });
	    
		$vpds = $this->getVpds();
		
		if ( count($vpds) > 1 )
		{
			$values = array_filter($iterator->getRowset(), function( $value ) use ( $vpds ) {
			    return in_array($value['VPD'], $vpds);
			});
			
			return $this->createCachedIterator( $values );
		}
		else
		{
		    return $iterator;
		}
	}

 	function getLatest( $limit = 10, $offset = 0 ) 
	{
	    $parent_getter = array($this, 'StoredObjectDB::getLatest');

	    return $this->getCachedQuery( GETTER_LATEST, function() use($parent_getter, $limit, $offset) 
	    { 
	        return call_user_func($parent_getter, $limit, $offset); 
	    });
	}

  	function getFirst( $limit = 1, $sorts = null ) 
	{
	    $parent_getter = array($this, 'StoredObjectDB::getFirst');

	    return $this->getCachedQuery( GETTER_FIRST, function() use($parent_getter, $limit, $sorts) 
	    { 
	        return call_user_func($parent_getter, $limit, $sorts); 
	    });
	}

   	function getCount() 
	{
	    $parent_getter = array($this, 'StoredObjectDB::getCount');

	    return $this->getCachedQuery( GETTER_COUNT, function() use($parent_getter) 
	    { 
	        return call_user_func($parent_getter); 
	    });
	}
	
	function getExact( $id ) 
	{
		if ( !$this->check_is_cacheable ) return parent::getExact( $id );
		
	    $parent_getter = array($this, 'StoredObjectDB::getAll');

	    $iterator = $this->getCachedQuery( GETTER_ALL, function() use($parent_getter) 
	    { 
	        return call_user_func($parent_getter); 
	    });
		
	    $ids = is_array($id) ? $id : array($id);

	    if ( count($ids) == 1 )
	    {
	    	$iterator->moveToId($ids[0]);
	    	
	    	return $iterator->copy();
	    }
	    
		$id_key = $this->getClassName().'Id';
		
		$data = array();

		foreach( $iterator->getRowset() as $key => $value )
		{
			if ( in_array($value[$id_key], $id) ) $data[] = $value;
		}

		return $this->createCachedIterator( $data );
	}
	
    function getByRefArray( $field_values, $limited_records = 0, $offset_page = 0) 
	{
	    global $model_factory;
	    
	    if ( !$this->check_is_cacheable )
		{
			if ( ALLOW_DEBUG ) $model_factory->debug( 'Skip cache "'.get_class($this).'" ('.$this->getClassName().') on getByRefArray: non cacheable' );
		    
		    return parent::getByRefArray( $field_values, $limited_records, $offset_page );
		}

		$values = $this->getAll()->getRowset(); 
		
		$result = array();
		
		foreach( $values as $key_row => $row )
		{
		    $row_found = true;
		    
		    foreach( $field_values as $attribute => $value )
		    {    
		        if ( is_array($value) )
		        {
		            $row_found = in_array($row[$attribute], $value);
		        }
		        else if ( is_a($value, 'OrderedIterator') )
		        {
		            $row_found = in_array($row[$attribute], $value->idsToArray());
		        }
		        else
		        {
    		        if ( $value == 'null' ) $value = '';
    		        
    		        if ( $value == 'NULL' ) $value = '';
    		        
    		        $match = array();
    		        
    		        if ( preg_match('/LCASE\(([^\)]+)\)/i', $attribute, $match) )
    		        {
    		            $attribute = $match[1]; 
    		            
    		            $row[$attribute] = strtolower($row[$attribute]);
    		        }
    		        
    		        $row_found = $row[$attribute] == $value;
		        }
		        
                if (!$row_found) break;
		    }
		    
		    if ( $row_found ) $result[] = $row;
		}

		if ( $limited_records > 0 )
		{
		    $result = array_slice($result, $offset_page * $limited_records, $limited_records);  
		}
	
		return $this->createCachedIterator( $result );
	}
	
	function add_parms( $parms )
	{
		$this->check_is_cacheable = false;
		
		$this->resetCache();
		
		$result = parent::add_parms( $parms );
		
		$this->check_is_cacheable = true;
		
		return $result;
	}
	
	function modify_parms( $id, $parms )
	{
		$this->check_is_cacheable = false;
		
		$this->resetCache();
		
		$result = parent::modify_parms( $id, $parms );
		
		$this->check_is_cacheable = true;
		
		return $result;
	}

	//----------------------------------------------------------------------------------------------------------
	function delete( $object_id, $record_version = ''  )
	{
		$this->check_is_cacheable = false;

		$this->resetCache();
		
		$result = parent::delete($object_id);
		
		$this->check_is_cacheable = true;
		
		return $result;		
	}
	
	function deleteAll()
	{
		$this->check_is_cacheable = false;
		
		$this->resetCache();
		
		$result = parent::deleteAll();

		$this->check_is_cacheable = true;
	}
}
