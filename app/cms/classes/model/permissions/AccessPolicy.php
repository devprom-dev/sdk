<?php

define('ACCESS_CREATE', 'add');
define('ACCESS_MODIFY', 'modify');
define('ACCESS_READ', 'read');
define('ACCESS_DELETE', 'delete');

class AccessPolicy
{
 	private $reason;
 	private $cache_service = null;
    private $cache_key = '';
 	private $data = array();
 	
 	function __construct( $cache_service = null, $cache_key = 'global' )
 	{
        $this->cache_key = $cache_key;
 		$this->setCacheService( is_object($cache_service) ? $cache_service : getFactory()->getCacheService() );
 		$this->data = $this->getCacheService()->get('access-policy-'.get_class($this), $this->cache_key);
 	}

 	function __destruct()
 	{
		$this->persistCache(); 		
 	}
 	
 	public function getCacheService() {
 		return $this->cache_service;
 	}
 	
 	public function setCacheService( $service ) {
 		$this->cache_service = $service;
 	}

    public function setCacheKey( $key ) {
        $this->cache_key = $key;
    }

    public function getCacheKey() {
        return $this->cache_key;
    }

 	public function invalidateCache()
 	{
 		$this->data = array();
 	}
 	
	function setReason( $reason )
	{
		$this->reason = $reason;
	}
	
	function getReason()
	{
		return $this->reason;
	}
	
	public function getSubjectKey()
	{
		return '';
	}
	
	function can_create( &$object ) 
	{
		if ( !is_object($object) ) return true;
		
		return $this->check_access( ACCESS_CREATE, $object, null );
	}

	function can_read_attribute( &$object, $attribute_refname, $reference_class = '' ) 
	{
        if ( !is_object($object) ) return true;
		return $this->check_access_attribute( ACCESS_READ, $object, $attribute_refname, $reference_class );
	}
	
	function can_modify_attribute( &$object, $attribute_refname, $reference_class = '' ) 
	{
		return $this->check_access_attribute( ACCESS_MODIFY, $object, $attribute_refname, $reference_class );
	}
	
	function can_read( &$target ) 
	{
		if ( !is_object($target) ) return true;
		
		if ( $target instanceof IteratorBase )
		{
			if ( $target->getId() == '' ) return false;
			return $this->check_access( ACCESS_READ, $target->object, $target );
		}
		else
		{
			return $this->check_access( ACCESS_READ, $target, null );
		}
	}

	function can_modify( $target )
	{
		if ( !is_object($target) ) return true;
		
		if ( $target instanceof IteratorBase ) {
			if ( $target->getId() == '' ) return $this->check_access( ACCESS_MODIFY, $target->object, null );
			return $this->check_access( ACCESS_MODIFY, $target->object, $target );
		}
		else {
			return $this->check_access( ACCESS_MODIFY, $target, null );
		}
	}

	function can_delete( &$target ) 
	{
		if ( !is_object($target) ) return true;
		
		if ( $target instanceof IteratorBase )
		{
			if ( $target->getId() == '' ) return false;
			
			return $this->check_access( ACCESS_DELETE, $target->object, $target );
		}
		else
		{
			return $this->check_access( ACCESS_DELETE, $target, null );
		}
	}

	function check_access( $action, &$object, $object_it ) 
	{
		return $this->check_roles_access( $action, $object, $object_it );
	}
	
	function check_access_attribute( $action, &$object, $attribute_refname, $reference_class ) 
	{
		return $this->check_roles_access_attribute($action, $object, $attribute_refname, $reference_class);
	}
	
	function check_roles_access_attribute( $action, &$object, $attribute_refname, $reference_class ) 
	{
		if ( !is_object($object) ) throw new Exception('Object is required');
		
		$key = $action.get_class($object).$object->getEntityRefName().$attribute_refname;
		
		$subject_key = $this->getSubjectKey();
		
		if ( $subject_key != '' )
		{
			$key .= $subject_key;
			
			if ( isset($this->data[$key]) ) return $this->data[$key];
		}
		 
		return $this->data[$key] = $this->getAttributeAccess($action, $object, $attribute_refname, $reference_class);
	}
	
	function check_roles_access( $action, &$object, $object_it ) 
	{
		if ( !is_object($object) ) throw new Exception('Object is required');
		
		$key = $action.get_class($object).$object->getEntityRefName().(!is_null($object_it) ? $object_it->getId() : '');
		
		$subject_key = $this->getSubjectKey();
		
		if ( $subject_key != '' )
		{
			$key .= $subject_key;
			
			if ( isset($this->data[$key]) ) return $this->data[$key];
		}

		$b_has_entity_access = $this->getEntityAccess($action, $object);
		
		$this->data[$key] = $b_has_entity_access;

		if ( $b_has_entity_access && is_object($object_it) ) 
		{
			$this->data[$key] = $this->getObjectAccess($action, $object_it);
		}
		
		return $this->data[$key];
	}
	
	/* */
 	public function getObjectAccess( $action_kind, &$object_it )
 	{
 		return true; 
 	}
	
 	public function getEntityAccess( $action_kind, &$object ) 
 	{
 		return true; 
 	}
 	
 	public function getAttributeAccess( $action_kind, &$object, $attribute_refname, $reference_class = '' ) 
 	{
 		return true; 
 	}
 	
	public function getReportAccess( $report ) 
 	{
 		return true; 
 	}
 	
 	private function persistCache()
 	{
 		$this->getCacheService()->set('access-policy-'.get_class($this), $this->data, $this->cache_key);
 	}
}