<?php

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class AttributeIterator extends OrderedIterator
 {
 	function AttributeIterator ( $object )
 	{
 		parent::OrderedIterator( $object );
 	}

	function getCaption() 
	{
		return $this->get('Caption');
	}
 }

 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class Attribute extends AggregatedObjectDB
 {
 	function __construct( $class_it = null, ObjectRegistrySQL $registry = null )
	{
		$this->attributes = array( 'Caption' => array('TEXT', 'Название', true, true, '', '', 10),
								   'ReferenceName' => array('TEXT', 'Ссылочное имя', true, true, '', '', 20),
								   'AttributeType' => array('TEXT', 'Тип свойства', true, true, '', '', 30),
								   'DefaultValue' => array('TEXT', 'Значение по умолчанию', true, true, '', '', 40),
								   'IsRequired' => array('CHAR', 'Обязательность', true, true, '', '', 50),
								   'IsVisible' => array('CHAR', 'Видимость', true, true, '', '', 60),
								   'entityId' => array('INTEGER', 'Сущность', false, true, '', '', 70),
								   'OrderNum' => array('INTEGER', 'Порядковый номер', true, true, '', '', 80)
								    );
		$this->defaultsort = 'OrderNum';

		parent::__construct( $class_it, $registry );
	}
	
	function addTableColumn( $object_id ) 
	{
		// alter database, load attributes for added attribute
		$attr = new Attribute;
		$attribute_it = $attr->getExact($object_id);

		// load metaobject for class that contains the attribute
		$entity = new Metaobject($this->container_it->get('ReferenceName'));
		$type_def = preg_split('/,/', $entity->getAttributeRDBMSDefinition($attribute_it->get('ReferenceName')));

		// construct sql-statement to add table fields		
		for($i = 0; $i < count($type_def); $i++) {
			$sql = 'ALTER TABLE '.$this->container_it->get('ReferenceName').' ADD '.$type_def[$i];
			mysql_query($sql) or die('SQL: '.$sql.', ERROR: '.mysql_error());
			
    		if(is_object(getFactory()))
    			if(getFactory()->sql_log_enabled) {
    				$sql_log = str_replace('\'', '\\\'', $sql);
    				$sql_log = "INSERT INTO SystemLogSQL (SQLContent, RecordCreated) VALUES ('".$sql_log."', NOW())";
    				$r3 = mysql_query($sql_log) or die('SQL: '.$sql_log.', ERROR: '.mysql_error());
    			}
		}
	}
	
	function dropTableColumn( $object_id )
	{
		// load attributes for deleted attribute
		$attr = new Attribute;
		$attribute_it = $attr->getExact($object_id);

		// load metaobject for class that contains the attribute
		$table_name = $this->container_it->get('ReferenceName');
		$entity = new Metaobject($table_name);
		$type_def = preg_split('/,/', $entity->getAttributeRDBMSDefinition($attribute_it->get('ReferenceName')));

		// alter database, drop table field
		for($i = 0; $i < count($type_def); $i++) {
			$column_def = preg_split('/\s/', trim($type_def[$i]));
			$sql = 'ALTER TABLE '.$table_name.' DROP '.$column_def[0];
			mysql_query($sql) or die('SQL: '.$sql.', ERROR: '.mysql_error());

    		if(is_object(getFactory()))
    			if(getFactory()->sql_log_enabled) {
    				$sql_log = str_replace('\'', '\\\'', $sql);
    				$sql_log = "INSERT INTO SystemLogSQL (SQLContent, RecordCreated) VALUES ('".$sql_log."', NOW())";
    				$r3 = mysql_query($sql_log) or die('SQL: '.$sql_log.', ERROR: '.mysql_error());
    			}
		}
	}
	
	function modify( $object_id )
	{
		global $_REQUEST;
		
		// check atrribute type has changed
		$attr = new Attribute;
		$attribute_it = $attr->getExact($object_id);

		$prev_attribute_type = $attribute_it->get('AttributeType');
		$new_attribute_type = $_REQUEST['AttributeType'];

		if( $prev_attribute_type != $new_attribute_type ) {
			$this->dropTableColumn( $object_id );
		}
		
		$result = parent::modify( $object_id );
		if ( $result < 1 ) return $result;

		if( $prev_attribute_type != $new_attribute_type ) {
			$this->addTableColumn( $object_id );
		}
		
		return $result;
	}
	
	//----------------------------------------------------------------------------------------------------------
	function getPageName() {
		$page = Object::getPageName();
		return $page.(strpos($page, '?') > 0 ? '&' : '?').'aggregateentity='.$this->container_it->object->getClassName().
					'&'.$this->container_it->object->getClassName().'Id='.$this->container_it->getId();
	}
	
	function getDefaultAttributeValue( $name ) 
	{
		if( $name == 'entityId' )	return $this->container_it->getId();
		if( $name == 'IsRequired' )	return 'Y';
		if( $name == 'IsVisible' )	return 'Y';
		return parent::getDefaultAttributeValue($name);
	}
	
	function createIterator() {
		return new AttributeIterator( $this );
	}

	function createDefaultView() {
		return new AttributeView( $this ); 
	}
	
  	function getClassName()
 	{
 		return 'attribute';
 	}
 }

 class AttributeRegistry extends ObjectRegistrySQL
 {
 	function createSQLIterator( $sql ) 
	{
	    $data = & _getAttributes();
	    
		return $this->createIterator( $data[$this->getObject()->getContainer()->get('ReferenceName')] );
	}
 }
 
 /////////////////////////////////////////////////////////////////////////////////////////////////////////
 class AttributeGenerated extends Attribute
 {
 	function __construct($entity_it)
 	{
 		parent::__construct($entity_it, new AttributeRegistry($this));
 	}
 }
