<?php

// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include SERVER_ROOT_PATH.'cms/c_object.php';
include SERVER_ROOT_PATH.'cms/c_entity.php'; 
include SERVER_ROOT_PATH.'cms/c_iterator_xml.php';
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectSQLPasswordPersister.php";

include "ObjectMetadataBuilder.php";
include "ObjectMetadataModelBuilder.php";
include "model/sorts/SortAttributeClause.php";
include "ObjectReferenceParser.php";
include "GroupAttributeClause.php";

define( 'GETTER_ALL', 'all' );
define( 'GETTER_LATEST', 'latest' );
define( 'GETTER_FIRST', 'first' );
define( 'GETTER_COUNT', 'count' );
 
class Metaobject extends StoredObjectDB
{
 	var $entity;
	var $reference_parsers;
	
	private $entity_ref_name = '';
	private $attributes_removed = array();
	private $entity_title = '';
	
 	function Metaobject( $entity_refname, ObjectRegistrySQL $registry = null, $metadata_cache_category = '' )
	{
	    global $entity_cache;
	    
		parent::__construct( $registry );
		
		if ( !is_object($entity_cache) ) $entity_cache = new EntityGenerated();
		
		$this->entity = $entity_cache->getByReference( $entity_refname );
		
		if ( $this->entity->getId() < 1 ) throw new Exception('Unknown entity: '.$entity_refname);
		
		$this->entity_ref_name = $this->entity->get('ReferenceName');
		$this->entity_title = $this->entity->getDisplayName();
		$this->reference_parsers = array();
		$this->resetFilters();
		
		if ( $metadata_cache_category == '' ) {
			$metadata_cache_category = getFactory()->getEntityOriginationService()->getCacheCategory($this);
		} 
			
		$metadata = getFactory()->getMetadataRegistry()->getMetadata($this, $metadata_cache_category);
		$metadata->setObject($this);

		$this->setPersisters( $metadata->getPersisters() ); 
		$attributes = $metadata->getAttributes();
		$this->attributes_removed = $metadata->getAttributesRemoved();
		
		$this->setAttributes($attributes);
		foreach( $attributes as $key => $attribute )
		{
			if ( $attribute['dbtype'] == 'PASSWORD' )
			{
				$this->addPersister( new ObjectSQLPasswordPersister() );
			}

			if ( $key == 'OrderNum' )
			{
			    $this->setSortDefault( new SortOrderedClause() );
			}
		}

	   	foreach( getSession()->getBuilders('ObjectModelBuilder') as $builder )
	    {
	        $builder->build( $this ); 
	    }
	}
	
	function getEntity()
	{
		return $this->entity;
	}
	
	function addReferenceParser( $parser )
	{
		foreach( $this->reference_parsers as $item )
		{
			if( get_class($item) == get_class($parser) ) return;
		}
		
		$parser->setObject( $this );
		array_push( $this->reference_parsers, $parser);
	}
	
	function IsReference( $attr )
	{
		$pos = strpos($this->getAttributeDbType( $attr ), 'REF_');
		return $pos !== false;
	}
	
	function getReferenceClassName( $attr )
	{
		$type = $this->getAttributeType($attr);
		
		$type = $this->getAttributeClassName( 
			$attr, substr($type, 4, strlen($type) - 6) );
			
		return $type;
	}
	
	function getClassName() 
	{
	    return $this->entity_ref_name;
	}
	
	function getEntityRefName() 
	{
	    return $this->entity_ref_name;
	}

	function getDisplayName() 
	{
		return preg_replace_callback (
			'/text\(([a-zA-Z\d]+)\)/i', iterator_text_callback, $this->entity_title
		);
	}
	
	function getPage() 
	{
		return '?';
	}

	function getPageName() {
		global $_REQUEST;
		$offset = $_REQUEST['offset'];
		return $this->getPage().'class=metaobject&entity='.$this->getClassName().(isset($offset) ? '&offset='.$offset : '');
	}

	function getPageTableName() {
		global $_REQUEST;
		$offset = $_REQUEST['offset'];
		return $this->getPage().'class=metaobject&entity='.$this->getClassName().'&view=table'.(isset($offset) ? '&offset='.$offset : '');
	}

	function getPageTableFiltered( $field, $value ) {
		global $_REQUEST;
		$offset = $_REQUEST['offset'];
		return $this->getPage().'class=metaobject&entity='.$this->getClassName().'&view=table'.
			   '&filter='.$field.'&filtervalue='.$value.(isset($offset) ? '&offset='.$offset : '');
	}
	
	function IsDeletedCascade( $object )
	{
		return true;
	}
	
	function IsUpdatedCascade( $object )
	{
		return true;
	}
	
	function IsOrdered()
	{
		return $this->entity->get('IsOrdered') == 'Y';
	}
	
	function DeletesCascade( $object )
	{
		return true;
	}

	function getAttributesRemoved()
	{
		return $this->attributes_removed;
	}

	//----------------------------------------------------------------------------------------------------------
 	function getAttributeUserName( $name ) 
 	{
 		switch ( $name )
 		{
 			case $this->getClassName().'Id':
 				return 'Идентификатор';

 			default:
 				return parent::getAttributeUserName( $name );
 		}
	}

	//----------------------------------------------------------------------------------------------------------
	function getAttributeClassName( $name, $class_name ) 
	{
		return $class_name;
	}

	//----------------------------------------------------------------------------------------------------------
	function getAttributeClass( $attribute )
	{
		$att_type = $this->getAttributeDbType( $attribute );
		
		return substr($att_type, 4, strlen($att_type) - 6);
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getAttributeObject( $attribute )
	{
		global $model_factory;
		
		$att_type = $this->getAttributeDbType( $attribute );
		
		if ( $att_type == '' ) 
		{
		    throw new Exception('There is no attribute "'.$attribute.'" in the class "'.get_class($this).'"');
		}

		if ( !$this->IsReference( $attribute ) )
		{
		    throw new Exception('Attribute "'.$attribute.'" of the class "'.get_class($this).'" is not a reference');
		}
		
	    foreach ( $this->reference_parsers as $parser )
		{
			$object = $parser->parse( $attribute, $att_type );
			
			if ( is_object($object) ) return $object;
		}

		$object = $model_factory->getObject($this->getAttributeClass($attribute));
		
		if ( !is_object($object) )
		{
		    throw new Exception('Attribute "'.$attribute.'" ('.$this->getAttributeDbType($attribute).') of the class "'.get_class($this).'" points to unknown class "'.$this->getAttributeClass($attribute).'"');
		}
		
        return $object;
	}
	
	//----------------------------------------------------------------------------------------------------------
	function createIterator() {
		return new OrderedIterator( $this );
	}

	//----------------------------------------------------------------------------------------------------------
	function createUnionIterator() {
		return new UnionIterator( $this );
	}

	//----------------------------------------------------------------------------------------------------------
	function createUnionIteratorFromArray( $iterator_array ) {
		$union_it = $this->createUnionIterator();
		for($i = 0; $i < count($iterator_array); $i++) {
			$union_it->addLink($iterator_array[$i]);
		}
		$union_it->moveFirst();
		return $union_it;
	}

	//----------------------------------------------------------------------------------------------------------
	function createSQLUnionIterator( $sql_query_array )
	{
		$union_it = $this->createUnionIterator();
		for($i = 0; $i < count($sql_query_array); $i++) {
			$union_it->addLink($this->createSQLIterator($sql_query_array[$i]));
		}
		$union_it->moveFirst();
		return $union_it;
	}

	//----------------------------------------------------------------------------------------------------------
	function createXMLIterator( $path )
	{
		return new IteratorXml( $this, $path );
	}

	//----------------------------------------------------------------------------------------------------------
	function createDefaultView() {
		global $_REQUEST;
		
		//if($_REQUEST[$this->entity->get("ReferenceName").'action'] != '') {
		if($_REQUEST['view'] == 'table') {
			return new ViewTable( $this ); 
		}
		else {
			return new MetaObjectView( $this ); 
		}
	}

	//----------------------------------------------------------------------------------------------------------
	function serialize2Xml()
	{
		$class_name = strtolower(get_class($this));
		
		if ( $class_name == 'metaobject' )
		{
			$class_name = $this->getClassName();
		}
		
		$xml = '<entity class="'.$class_name.'" encoding="'.APP_ENCODING.'">';
		
		$object_it = $this->getAll();
		$xml .= $object_it->serialize2Xml(); 
				
		return $xml . '</entity>';
	}
	
	function RemoteLoad( $token, $id )
	{
		global $soap;
		return $soap->load( $token, get_class($this), $id );
	}

	function RemoteAdd( $token, $parms )
	{
		global $soap;
		return $soap->add( $token, get_class($this), $parms );
	}

	function RemoteAddBatch( $token, $parms )
	{
		global $soap;
		return $soap->addBatch( $token, get_class($this), $parms );
	}
	
	function RemoteStore( $token, $id, $parms )
	{
		global $soap;
		return $soap->store( $token, get_class($this), $id, $parms );
	}

	function RemoteStoreBatch( $token, $parms )
	{
		global $soap;
		return $soap->storeBatch( $token, get_class($this), $parms );
	}
	
	function RemoteDelete( $token, $id )
	{
		global $soap;
		return $soap->delete( $token, get_class($this), $id );
	}

	function RemoteDeleteBatch( $token, $parms )
	{
		global $soap;
		
		return $soap->deleteBatch( $token, get_class($this), $parms );
	}
	
	function RemoteGetAll( $token )
	{
		global $soap;
		return $soap->getAll( $token, get_class($this) );
	}

	function RemoteFind( $token, $parms )
	{
		global $soap;
		return $soap->find( $token, get_class($this), $parms );
	}

	//----------------------------------------------------------------------------------------------------------
	function delete( $object_id )
	{
		global $array_to_delete;
		
		if( !is_array($array_to_delete) ) $array_to_delete = array();

		$key = get_class($this).','.$object_id;

		// check if object has been deleted already
		if( in_array($key, $array_to_delete) ) return;
		
		$array_to_delete[] = $key;

		$self_it = $this->getExact($object_id);

		$deleted_list = array();
		
		$modified_list = array();
		
		// get items references to the current one
		$references = getFactory()->getModelReferenceRegistry()->getBackwardReferences($this);

		// delete objects have references to the given one
		foreach ( $references as $attribute_path => $class_name )
		{
		    $parts = preg_split('/::/', $attribute_path);
		    
		    $attribute = $parts[1];
		    
		    $object = getFactory()->getObject($class_name);
		    
		    if ( !$object->IsAttributeStored($attribute) ) continue;

		    $object->setVpdContext( $self_it );
		    
		    $object->setNotificationEnabled(false);
		    
			if ( $this->DeletesCascade($object) && $object->IsDeletedCascade($this) )
			{
    			$object_it = $object->getRegistry()->Query( 
    					array (
    							new FilterAttributePredicate($attribute,$object_id) 
    					)
    			);

				while( $object_it->getId() != '' )
				{
					$deleted_list[] = $object_it->copy();
					
				    $object->delete( $object_it->getId() );
					
					$object_it->moveNext();
				}
			}
			elseif ( $object->IsUpdatedCascade($this) )
			{
				$reference_it = $object->getRegistry()->Query(
					array (
						new FilterAttributePredicate($attribute,$object_id)
					)
				);

				$modified_list[] = $object->createCachedIterator($reference_it->getRowset());
				
				$this->UpdatesCascade( $attribute, $self_it, $reference_it ); 
			}
			
			$object->enableVpd();
		}

		$result = parent::delete($object_id);
		
		foreach( $deleted_list as $object_it )
		{
			getFactory()->getEventsManager()->notify_object_delete($object_it);
		}
		
		foreach( $modified_list as $object_it )
		{
			while( !$object_it->end() )
			{
				getFactory()->getEventsManager()->notify_object_modify($object_it, $object_it, array());
				
				$object_it->moveNext();
			}
		}
		
		return $result;
	}
	
	function UpdatesCascade( $attribute, & $self_it, & $reference_it )
	{
		while( $reference_it->getId() != '' )
		{
			$reference_it->object->modify_parms($reference_it->getId(), array( $attribute => '' ));
			
			$reference_it->moveNext();
		}
	}

	public function __sleep()
	{
		throw new Exception('Unable serialize Metaobject');
	}
}
