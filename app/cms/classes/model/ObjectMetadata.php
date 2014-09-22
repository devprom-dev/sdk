<?php

class ObjectMetadata 
{
	public function __construct( Metaobject $object = null, array $attributes = array(), array $persisters = array() )
	{
		$this->setObject( $object );
		
		$this->setAttributes( $attributes );
		
		$this->setPersisters( $persisters );
	}

    public function build()
    {
    	foreach( getSession()->getBuilders('ObjectMetadataBuilder') as $builder )
	    {
	        $builder->build( $this ); 
	    }
    }
	
	public function setObject( $object )
	{
		$this->object = $object;
	}
	
	public function getObject()
	{
		return $this->object;
	}
	
 	function addAttribute( $ref_name, $type, $caption, $b_visible, $b_stored = false, $description = '', $ordernum = 999 )
 	{
		if ( $ordernum == 999 ) $ordernum = $this->getLatestOrderNum() + 10;
 		
 		$this->attributes[$ref_name] = array (
 				'dbtype' => $type,
 				'caption' => $caption,
 				'visible' => $b_visible,
 				'stored' => $b_stored,
 				'type' => $type,
 				'description' => $description,
 				'ordernum' => $ordernum,
 				'required' => false
 		);
 	} 	
	
    public function setAttribute( $referenceName, $data )
    {
        $this->attributes[$referenceName] = $data;
    }
    
    function getAttributes()
    {
        return $this->attributes;
    }
    
    function setAttributes( $attributes )
    {
        $this->attributes = $attributes;
    }
    
    function getPersisters()
    {
    	return $this->persisters;
    }
    
    function setPersisters( $persisters )
    {
    	$this->persisters = $persisters;
    }
    
    function addPersister( $persister )
    {
    	$this->persisters[] = $persister;
    }
    
    function setAttributeVisible( $attribute, $visible = true )
    {
    	$this->attributes[$attribute]['visible'] = $visible;
    }
    
    public function removeAttribute($attribute)
    {
    	unset($this->attributes[$attribute]);
    }
    
    public function setAttributeType($attribute, $type)
    {
    	$this->attributes[$attribute]['type'] = $type;
    }
    
    public function setAttributeCaption($attribute, $caption)
    {
    	$this->attributes[$attribute]['caption'] = $caption;
    }
    
    public function getAttributeCaption($attribute)
    {
    	return $this->attributes[$attribute]['caption'];
    }
    
    public function setAttributeRequired($attribute, $required = true)
    {
    	$this->attributes[$attribute]['required'] = $required;
    }
    
    public function getAttributeRequired($attribute)
    {
    	return $this->attributes[$attribute]['required'];
    }
    
    public function setAttributeDescription($attribute, $text)
    {
    	$this->attributes[$attribute]['description'] = $text;
    }
    
    public function getAttributeDescription($attribute)
    {
    	return $this->attributes[$attribute]['description'];
    }
    
    public function setAttributeOrderNum($attribute, $ordernum)
    {
    	$this->attributes[$attribute]['ordernum'] = $ordernum;
    }
    
    public function getAttributeOrderNum()
    {
    	return $this->attributes[$attribute]['ordernum'];
    }
    
    public function getLatestOrderNum()
    {
    	$max = 10;
    	
    	foreach( $this->attributes as $attribute )
    	{
    		if ( $attribute['ordernum'] > $max ) $max = $attribute['ordernum']; 
    	}
    	
    	return $max;
    }
    
	function getAttributesByGroup( $group )
	{
		$attributes = array_filter( $this->attributes, function($value) use ($group) 
		{
			return is_array($value['groups']) && in_array($group, $value['groups']);
		});
		
		return array_keys($attributes);
	}
	
	function addAttributeGroup( $name, $group )
	{ 
		if ( !array_key_exists($name, $this->attributes) ) return;
		
		if ( !isset($this->attributes[$name]['groups']) )
		{
			$this->attributes[$name]['groups'] = array();
		}
		
		$this->attributes[$name]['groups'][] = $group;
	}
    
    private $object = array();
    
    private $attributes = array();
    
    private $persisters = array();
}