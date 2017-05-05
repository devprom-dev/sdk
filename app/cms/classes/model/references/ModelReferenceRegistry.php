<?php

define(MODEL_REFERENCES_CACHE_KEY, 'model-references');

class ModelReferenceRegistry extends ObjectRegistrySQL
{
	private $reference_forward = array();
	private $reference_backward = array();
	private $cache_service = null;
    private $cache_key = '';
	
	public function __construct( $cache_service, $cache_key = 'global' )
	{
		$this->cache_service = $cache_service;
        $this->cache_key = $cache_key;
		
		list($this->reference_forward, $this->reference_backward) = 
				$this->cache_service->get(MODEL_REFERENCES_CACHE_KEY, $this->cache_key);
		
		if ( !$this->reference_forward ) {
			$this->buildStaticReferences();
		}		 
	}
	
	function __destruct()
	{
		$this->cache_service->set(MODEL_REFERENCES_CACHE_KEY, 
            array (
                $this->reference_forward,
                $this->reference_backward
            ),
            $this->cache_key
		);
	}

    public function setCacheKey( $key ) {
        $this->cache_key = $key;
    }

	function addObjectReferences( & $object )
	{
		if ( !$object instanceof Metaobject ) return;
		
		$key = $this->getClassName($object);
		
		if ( isset($this->reference_forward[$key]) ) return;

		foreach( $object->getAttributes() as $attr => $value )
		{ 
		    if ( !$object->IsReference( $attr ) ) continue;
			
			$class = $object->getAttributeClass($attr);
			
			if ( $class == '' ) continue;
			
			$this->addReference( $object, getFactory()->getClass($class), $attr );
		}
		
		if ( !isset($this->reference_forward[$key]) )
		{
			$this->reference_forward[$key] = array();
		}
	}
	
 	function addReference( $from, $to, $attribute )
	{
		$from_class = $this->getClassName($from);

		$to_class = $this->getClassName($to);
		
		if ( !is_array($this->reference_backward[$to_class]) ) $this->reference_backward[$to_class] = array();
		
		$this->reference_backward[$to_class] = array_merge($this->reference_backward[$to_class], array (
		        $from_class.'::'.$attribute => $from_class
		)); 

		if ( !is_array($this->reference_forward[$from_class]) ) $this->reference_forward[$from_class] = array();
		
		$this->reference_forward[$from_class] = array_merge($this->reference_forward[$from_class], array(
		        $to_class.'::'.$attribute => $to_class 
		)); 
	}

	function getBackwardReferences( $object )
	{
		$key = $this->getClassName($object);
		
		$references = is_array($this->reference_backward[$key]) ? $this->reference_backward[$key] : array();

		// inherited references
		
		if ( is_object($object) )
		{
			$references = array_merge($references, is_array($this->reference_backward[$object->getEntityRefName()]) 
			    ? $this->reference_backward[$object->getEntityRefName()] : array());
		}
		
		return $references;
	}
	
	function getReferences()
	{
	    return array( $this->reference_forward, $this->reference_backward );
	}

	private function getClassName( $object )
	{
		return is_object($object) 
			? (strtolower(get_class($object)) == 'metaobject' ? $object->getClassName() : get_class($object))
			: $object;
	}
	
	private function buildStaticReferences()
	{
		include_once SERVER_ROOT_PATH."cms/references.php";
		
		$this->reference_forward = _getForwardReferences();
		
		$this->reference_backward = _getBackwardReferences();
	}
}