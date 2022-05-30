<?php
 
 include('c_attribute.php');
 include('c_package.php');
 include('c_generated.php');

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class EntityIterator extends OrderedIterator
 {
 	function getCaption() 
 	{
		return translate($this->get('Caption'));
	}
	
 	function getDisplayName() 
 	{
		return translate($this->get('Caption'));
	}
	
	function getDescription() 
	{
		$richedit = new FieldRichEdit;
		return $richedit->decode($this->get('Description'));
	}

	function getAttributes()
	{
		return new Attribute ( $this );
	}
	
	function getClassName() 
	{
		return $this->get('ReferenceName');
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class Entity extends StoredObjectDB
 {
 	function __construct( ObjectRegistrySQL $registry = null )
	{
		$this->attributes = array( 'Caption' => array('TEXT', 'Название', true, true),
								   'ReferenceName' => array('TEXT', 'Название таблицы', true, true),
								   'packageId' => array('INTEGER', 'Пакет', true, true),
								   'IsOrdered' => array('CHAR', 'Порядок хранения экземпляров', true, true),
								   'IsDictionary' => array('CHAR', 'Справочник', true, true),
								   'OrderNum' => array('INTEGER', 'Порядковый номер', true, true)
								    );
		
		$this->defaultsort = 'OrderNum';
		$this->entity = $this->createIterator();

		// aggregates own atributes
		$this->aggregates = array(new Attribute);

		parent::__construct( $registry );
	}
	
    function getClassName()
 	{
 		return 'entity';
 	}

    function getAttributes() {
         return $this->attributes;
    }

	function delete( $id, $record_version = '' )
	{
		$entity = $this->getExact($id);
		
		$metaobject = new Metaobject($entity->get('ReferenceName'));
		$metaobject->UnInstall();
	
		return parent::delete( $id );
	}
	
	function isAttributeRequired( $name ) {
		if( $name == 'ReferenceName' ) return true;
		if( $name == 'packageId' ) return true;
		return parent::isAttributeRequired( $name );
	}
	
	function createIterator() {
		return new EntityIterator( $this );
	}

	function createDefaultView() {
		return new EntityView( $this ); 
	}
	
	function getHashed( $ref_name )
	{
		return $this->getByRef('ReferenceName', $ref_name);
	}
	
	function isVpdEnabled()
	{
		return false;
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class EntityGeneratedIterator extends EntityIterator
 {
	function getAttributeIt()
	{
		$attribute = new AttributeGenerated ( $this );
		
		return $attribute->getAll();
	}
 }

 class EntityRegistrySQL extends ObjectRegistrySQL
 {
 	function createSQLIterator( $sql ) 
	{
	    return $this->createIterator( _getEntities() );
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class EntityGenerated extends Entity
 {
     var $data;
     
     function __construct()
     {
     	parent::__construct( new EntityRegistrySQL($this) );
     }
     
 	function createIterator()
 	{
 	    return new EntityGeneratedIterator($this);
 	}
 	
	function getByReference( $ref_name )
	{
	    $ref_name = strtolower($ref_name);

	    if ( !isset($this->data) ) $this->data = $this->getAll()->getRowset();
	     
	    foreach( $this->data as $row )
	    {
	        if ( $row['ReferenceNameLC'] == $ref_name ) return $this->createCachedIterator(array($row));
	    }

	    /*
	    $it = $this->getAll();
	    
	    while( !$it->end() )
	    {
	        if ( strcasecmp($it->get('ReferenceName'), $ref_name) == 0 ) return $it->getCurrentIt();
	        
	        $it->moveNext();
	    }
	    */
	    
	    return $this->getEmptyIterator();
	}
 }
