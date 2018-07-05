<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class ObjectMetadata 
{
    private $object = null;
    private $attributes = array();
	private $removed = array();
    private $persisters = array();
	
	public function __construct( Metaobject $object = null, array $attributes = array(), array $persisters = array() )
	{
		$this->setObject( $object );
		$this->setAttributes( $attributes );
		$this->setPersisters( $persisters );
	}

	public function __sleep()
	{
		unset($this->object);
		$this->object = null;
		return array('attributes', 'persisters', 'removed');
	}
	
	public function __destruct()
	{
		unset($this->object);
		$this->object = null;
	}
	
	public function __wakeup()
	{
		$this->object = null;
	}
	
    public function build()
    {
    	foreach( $this->getBuilders() as $builder ) {
	        $builder->build( $this ); 
	    }
    }

    protected function getBuilders()
    {
        $builders = array (
            new ObjectMetadataModelBuilder()
        );

        $session = getSession();
        if ( is_object($session) ) {
            $builders = array_merge( $builders, $session->getBuilders('ObjectMetadataBuilder') );
        }

        return $builders;
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
		$persister->setObject($this->object);
    	$this->persisters[] = $persister;
    }
    
    function setAttributeVisible( $attribute, $visible = true )
    {
    	$this->attributes[$attribute]['visible'] = $visible;
    }

    public function removeAttribute($attribute)
	{
		$this->removed[$attribute] = $this->attributes[$attribute];
		unset($this->attributes[$attribute]);
	}

	public function getAttributesRemoved() {
		return $this->removed;
	}

    public function setAttributeType($attribute, $type) {
    	$this->attributes[$attribute]['type'] = $this->attributes[$attribute]['dbtype'] = $type;
    }

	public function getAttributeType($attribute) {
		return $this->attributes[$attribute]['type'];
	}

	public function hasAttributesOfType( $type ) {
		return count(array_filter($this->attributes, function($attribute) use($type) {
			return strtolower($attribute['type']) == strtolower($type);
		}));
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

    public function setAttributeDefault($attribute, $defaultValue)
    {
        $this->attributes[$attribute]['default'] = $defaultValue;
    }

    public function getAttributeDefault($attribute)
    {
        return $this->attributes[$attribute]['default'];
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
    
    public function getAttributeOrderNum($attribute)
    {
    	return $this->attributes[$attribute]['ordernum'];
    }

    public function setAttributeEditable($attribute, $editable = true)
    {
        $this->attributes[$attribute]['editable'] = $editable;
    }

    public function getAttributeEditable($attribute)
    {
        return $this->attributes[$attribute]['editable'];
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
		if ( !isset($this->attributes[$name]['groups']) ) {
			$this->attributes[$name]['groups'] = array();
		}
		$this->attributes[$name]['groups'][] = $group;
	}

	function resetAttributeGroup( $name, $group )
	{
		if ( !array_key_exists($name, $this->attributes) ) return;
		foreach( $this->attributes[$name]['groups'] as $key => $ingroup ) {
			if ( $ingroup == $group ) unset($this->attributes[$name]['groups'][$key]);
		}
	}
	
	function getAttributeClass( $attribute )
	{
		return strpos($this->attributes[$attribute]['type'], 'REF_') !== false
				? substr($this->attributes[$attribute]['type'], 4, strlen($this->attributes[$attribute]['type']) - 6)
				: '';
	}

    function IsReference( $attr ) {
        return strpos($this->getAttributeType($attr), 'REF_') !== false;
    }
}