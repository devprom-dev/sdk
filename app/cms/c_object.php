<?php

 define ('SHADOW_PASS', '************');

define( 'ORIGIN_METADATA', 'metadata' );
define( 'ORIGIN_COMPUTED', 'computed' );
 
include('c_iterator.php'); 
 include('c_iterator_union.php');
 
 include_once SERVER_ROOT_PATH."cms/classes/model/StoredObjectDB.php";

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class AbstractObject
 {
 	var $attributes = array();
	var $classname;
	var $defaultsort;
	var $model_factory;
	
	function createIterator()
    {
		return null;
	}
	
	function getExcelIterator( $parms ) 
	{
		return null;
	}
	
	function hasAttribute( $name )
	{
		return array_key_exists($name, $this->attributes);
	}
	
 	function addAttribute( $ref_name, $type, $caption, $b_visible = true, $b_stored = false, $description = '', $ordernum = 999 )
	{
		if ( $ordernum == 999 ) $ordernum = $this->getLatestOrderNum() + 10;
		
		$this->attributes[$ref_name] = array( 
				'caption' 		=> $caption,
				'visible' 		=> $b_visible, 
				'stored'  		=> $b_stored, 
				'description' 	=> $description, 
				'ordernum' 		=> $ordernum,
				'origin' 		=> ORIGIN_METADATA
		);
		$this->setAttributeType($ref_name, $type);

        uasort( $this->attributes, "attribute_sort_ordernum" );
	}
	
	function removeAttribute( $attr )
	{
		unset($this->attributes[$attr]);
	}
	
	function getIdAttribute()
	{
		return $this->getEntityRefName().'Id';
	}
	
 	function getAttributeType( $name ) 
 	{
 		switch ( strtolower($name) )
 		{
 			case strtolower($this->getClassName().'Id'):
 				return 'integer';
 				
 			default:
				return strtolower($this->attributes[$name]['type']);
 		}
	}

 	function getAttributeDbType( $name ) 
 	{
		return $this->attributes[$name]['dbtype'];
	}

 	function getAttributeDbName( $name ) {
		return $name;
	}

	function getAttributeVisualType( $name ) 
	{
		return $this->getAttributeType( $name );
	}
	
	function getAttributeTypeName( $name ) 
	{
		switch ( $this->getAttributeType( $name ) )
		{
			case 'integer':
				return translate('число');

			case 'float':
				return translate('число с запятой');

			case 'date':
			case 'datetime':
				return translate('дата');
				
			case 'char':
				return translate('булевое значение');

			case 'text':
			case 'varchar':
				return translate('текст');
				
			default:
				return '';
		}
	}

 	function getAttributeUserName( $name ) 
 	{
		return preg_replace_callback (
		    '/text\(([a-zA-Z\d]+)\)/i',
            iterator_text_callback,
            $this->attributes[$name]['caption']
		);
	}
	
 	function getAttributeOrderNum( $name ) 
 	{
		return $this->attributes[$name]['ordernum'];
	}

     function getAttributeEditable( $name )
     {
         if ( !array_key_exists($name, $this->attributes) ) return true;
         return array_key_exists('editable', $this->attributes[$name]) ? $this->attributes[$name]['editable'] : true;
     }

 	function getAttributeDescription( $name )
 	{
		return preg_replace_callback (
			'/text\(([a-zA-Z\d]+)\)/i', iterator_text_callback, $this->attributes[$name]['description'] 
		);
	}
	
	function getAttributeGroups( $name )
	{
		return $this->attributes[$name]['groups'];
	}
	
	function getAttributesByGroup( $group )
	{
		$attributes = array_filter( $this->getAttributes(), function($value) use ($group) {
			return is_array($value['groups']) && in_array($group, $value['groups']);
		});
		
		return array_keys($attributes);
	}

	function resetAttributeGroup( $attribute, $group ) {
        if ( !array_key_exists($attribute, $this->attributes) ) return;
        $this->attributes[$attribute]['groups'] =
            array_diff($this->attributes[$attribute]['groups'], array($group));
    }

	 function getAttributesByOrigin( $origin ) {
		 $attributes = array_filter( $this->attributes, function($value) use ($origin) {
			 return $value['origin'] == $origin;
		 });
		 return array_keys($attributes);
	 }

	 function getAttributeByCaption( $caption ) {
		 $attributes = array_filter( $this->attributes, function($value) use ($caption) {
			 return $value['caption'] == $caption;
		 });
		 return array_shift(array_keys($attributes));
	 }

	 function getAttributesByType( $type ) {
		 $attributes = array_filter( $this->attributes, function($value) use ($type) {
			 return strcasecmp($value['type'], $type) === 0;
		 });
		 return array_keys($attributes);
	 }

 	//----------------------------------------------------------------------------------------------------------
	function getAttributeOrigin( $name ) 
	{
        if ( !array_key_exists($name, $this->attributes) ) return '';
		return $this->attributes[$name]['origin'];
	}
	
	//----------------------------------------------------------------------------------------------------------
	function attributesHasOrigin( $origin ) 
	{
		foreach( $this->attributes as $key => $value ) {
			if ( $value['origin'] == $origin ) return true;
		}
		return false;
	}
	
 	function IsAttributeRequired( $name ) 
	{
        if ( !array_key_exists($name, $this->attributes) ) return false;
		return $this->attributes[$name]['required'];
	}
	
 	function getDefaultAttributeValue( $name ) 
	{
        if ( !array_key_exists($name, $this->attributes) ) return '';
		return $this->attributes[$name]['default'];
	}
	
  	function setAttributeOrigin( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    $this->attributes[$ref_name]['origin'] = $value;
	}
	
	function setAttributeDefault( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    $this->attributes[$ref_name]['default'] = $value;
	}
	
	function setAttributeRequired( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    $this->attributes[$ref_name]['required'] = $value;
	}

     function setAttributeEditable( $ref_name, $value )
     {
         if ( !array_key_exists($ref_name, $this->attributes) ) return;
         $this->attributes[$ref_name]['editable'] = $value;
     }

	function setAttributeGroups( $name, array $groups )
	{
	    if ( !array_key_exists($name, $this->attributes) ) return;
	    $this->attributes[$name]['groups'] = $groups;
	}
	
	function addAttributeGroup( $name, $group )
	{ 
	    if ( !array_key_exists($name, $this->attributes) ) return;
		if ( !isset($this->attributes[$name]['groups']) ) {
			$this->attributes[$name]['groups'] = array();
		}
		$this->attributes[$name]['groups'][] = $group;
	}
	
	function setAttributeCaption( $ref_name, $caption )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
		$this->attributes[$ref_name]['caption'] = $caption;
	}
	
	function setAttributeDescription( $ref_name, $text )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    $this->attributes[$ref_name]['description'] = $text;
	}
	
	function setAttributeVisible( $ref_name, $visible )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    $this->attributes[$ref_name]['visible'] = $visible;
	}
	
	function setAttributeOrderNum( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    
	    $this->attributes[$ref_name]['ordernum'] = $value;
        uasort( $this->attributes, "attribute_sort_ordernum" );
	}
	
 	function setAttributeStored( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    $this->attributes[$ref_name]['stored'] = $value;
	}
	
  	function setAttributeType( $ref_name, $value )
	{
	    if ( !array_key_exists($ref_name, $this->attributes) ) return;
	    
		$datatype = $value;
		
		if ($datatype == 'RICHTEXT') $datatype = 'TEXT';
		if ($datatype == 'LARGETEXT') $datatype = 'TEXT';
		
	    
	    $this->attributes[$ref_name]['dbtype'] = $value;
	    $this->attributes[$ref_name]['type'] = $datatype;
	}
	
	function isAttributeVisible( $name ) 
	{
		return $this->attributes[$name]['visible'];
	}

	function IsAttributeStored( $name ) 
	{
		return $this->attributes[$name]['stored'];
	}

     function IsAttributePersisted( $name )
     {
         return $this->IsAttributeStored($name) || $this->getAttributeOrigin($name) == ORIGIN_CUSTOM;
     }

	function getAttributes()
	{
		return $this->attributes;
	}

    function getAttributesVisible()
	{
		 return array_keys(
			 array_filter( $this->attributes,
				 function($attribute) {
					 return $attribute['visible'];
			 	 }
			 )
		 );
	}

    function getAttributesRequired()
    {
         return array_keys(
             array_filter(
                 $this->attributes,
                 function($attribute) {
                     return $attribute['required'];
                 }
             )
         );
    }

 	function setAttributes( $attributes )
	{
		$this->attributes = $attributes;
        uasort( $this->attributes, "attribute_sort_ordernum" );
	}
	
	function getLatestOrderNum()
	{
		$latest = 0;
		
		foreach( $this->attributes as $attribute )
		{
			if ( $attribute['ordernum'] > $latest ) $latest = $attribute['ordernum'];  
		}
		
		return $latest;
	}
	
	function getClassName() {
		return strtolower(get_class($this));
	}

 	function getDisplayName() {
		return strtolower(get_class($this));
	}
	
	function getPageName() {
		global $_REQUEST;
		$offset = $_REQUEST['offset1'];
		return 'object.php?class='.$this->getClassName().(isset($offset) ? '&offset='.$offset : '');
	}

	function getPageNameObject( $object_id = '' ) {
		return $this->getPageName().'&'.$this->getEntityRefName().'Id='.$object_id;
	}

	function getPageNameEditMode( $objectid ) {
		return $this->getPageNameObject( $objectid ).'&'.$this->getEntityRefName().'action=show';
	}

	function getPageNameViewMode( $objectid ) {
		return $this->getPageNameObject( $objectid ).'&'.$this->getEntityRefName().'action=view';
	}

	function getPageNameCreateLike( $objectid ) {
		return $this->getPageNameObject( $objectid ).'&'.$this->getEntityRefName().'action=createlike';
	}
	
	function isOrdered() {
		return false;
	}
	
	function createDefaultView() {
		return new ViewBasic( $this );
	}
	
	function createForm() {
		return new Form( $this );
	}

	function createListForm() {
		return new ListForm( $this );
	}

	 // TODO: obsolete
	function Utf8ToWin($fcontents) 
	{
		return $fcontents;
	}
 }

 //////////////////////////////////////////////////////////////////////////////////////////////
 function attribute_sort_ordernum( $left, $right )
 {
 	return $left['ordernum'] > $right['ordernum'] ? 1 : -1;
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 //-----------------------------------------------------------------------------------------------------//
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class AggregatedObjectDB extends StoredObjectDB
 {
 	var $container_it;
	
	//----------------------------------------------------------------------------------------------------------
	function __construct( $container_it, ObjectRegistrySQL $registry = null )
	{
		$this->container_it = $container_it;
		
		parent::__construct( $registry );
	}
	
	public function & getContainer()
	{
		return $this->container_it;
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getAll() 
	{
		return $this->getByRef( $this->container_it->object->getEntityRefName().'Id', $this->container_it->getId() );
	}

	//----------------------------------------------------------------------------------------------------------
	function getPageName() {
		$page = $this->container_it->object->getPageName();
		return $page.(strpos($page, '?') > 0 ? '&' : '?').$this->container_it->object->getClassName().'Id='.
					$this->container_it->getId().'&'.$this->container_it->object->getClassName().'action=show';
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class SingletonDB extends StoredObjectDB
 {
 	function Install()
	{
		parent::Install();
		parent::add();
	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////////////
 class AggregateBase
 {
 	var $object, $alias, $attribute, $agg_attribute, $aggregate, $group_function;
 	
 	function AggregateBase( $attribute, $agg_attribute = '1', $aggregate = 'COUNT' )
 	{
 		$this->alias = 't';
 		
 		$this->attribute = DAL::Instance()->Escape($attribute);
 		$this->aggregate = DAL::Instance()->Escape($aggregate);
 		$this->agg_attribute = DAL::Instance()->Escape($agg_attribute);
 	}
 	
 	function setObject( $object )
 	{
 		$this->object = $object;
 		
 	 	if ( in_array($this->object->getAttributeType($this->attribute), array('date', 'datetime')) && $this->group_function == '' )
 		{
 			$this->setGroupFunction('UNIX_TIMESTAMP');
 		}
 	}
 	
 	function getObject()
 	{
 		return $this->object;
 	}

 	function setAlias( $alias )
 	{
 		$this->alias = $alias;
 	}

 	function setGroupFunction( $function )
 	{
 		$function = strtolower(DAL::Instance()->Escape($function));
 		
 		switch ( $function )
 		{
 			case 'to_days':
 			case 'week':
 			case 'month':
 			case 'quarter':
 			case 'year':
                $this->group_function = $function.'(';
                break;

 			case 'unix_timestamp':
 				$this->group_function = '(SELECT @row_num:=@row_num+1) + '.$function.'(';
				break;

 			default:
 			    if ( preg_match('/extract/', $function) )
 			    {
 			        $this->group_function = $function;
 			    }
 			    else
 			    {
 			        return '';
 			    }
 		}
 	}
 	
 	function getAlias()
 	{
 		return $this->alias;
 	}

	function getAttribute()
	{ 	
		return $this->attribute;
	}
	
	function getAggregate()
	{ 	
		return $this->aggregate;
	}

	function getAggregatedAttribute()
	{ 	
		return $this->agg_attribute != '1'
			? $this->agg_attribute : '';
	}

	function getAggregatedInnerColumn()
	{ 	
		return $this->getAggregatedAttribute() == '' 
			? '' : ($this->getAlias() != '' ? $this->getAlias().'.'.$this->getAggregatedAttribute() : $this->getAggregatedAttribute());
	}
	
	function getAggregateAlias()
	{ 	
		return 'column'.md5($this->attribute.$this->aggregate.$this->agg_attribute);
	}

 	function getColumn()
 	{
 	    if ( is_numeric($this->attribute) ) return $this->attribute;
 		return $this->getAlias() != '' ? $this->getAlias().'.'.$this->attribute : $this->attribute;
 	}

 	function getInnerColumn()
 	{
 		$computed = $this->object->getAttributeDbType($this->getAttribute()) != '' && !$this->object->IsAttributeStored($this->getAttribute());

 	 	if ( $computed )
 		{
 			return $this->attribute; 
 		}
 		else
 		{
	 		return $this->group_function != '' 
	 			? $this->group_function.$this->getColumn().') '.$this->attribute : $this->getColumn(); 
 		}
 	}
 	
 	function getAggregateColumn()
 	{
 		switch ( strtolower($this->aggregate) )
 		{
 			case 'none':
 				return $this->agg_attribute != '1' 
 					? $this->agg_attribute.' '.$this->getAggregateAlias() : '';
 				
 			case 'count':
 			case 'sum':
 			case 'max':
 			case 'min':
 			case 'avg':
 			case 'group_concat':
 				return $this->aggregate.'('.$this->agg_attribute.') '.$this->getAggregateAlias();
 				
 			default:
 				return '1';
 		}
 	}
 }
 