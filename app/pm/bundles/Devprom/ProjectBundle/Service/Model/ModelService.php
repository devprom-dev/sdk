<?php
namespace Devprom\ProjectBundle\Service\Model;

class ModelService
{
	public function __construct( $validator_serivce, $mapping_service, $filter_resolver )
	{
		$this->validator_service = $validator_serivce;
		
		$this->mapping_service = $mapping_service;
		
		$this->filter_resolver = $filter_resolver;
	}
	
	public function set( $entity, $data, $id = '' )
	{
		$object = is_object($entity) ? $entity : $this->getObject($entity);

		foreach( $data as $key => $value )
		{
			if ( is_array($value) && $object->IsReference($key) )
			{
				// resolve subobjects into references
				$ref = $object->getAttributeObject($key);
				$data[$key] = $ref->getRegistry()->Query($this->buildSearchQuery($ref, $value))->getId(); 
			}
			else
			{
				if ( $key == 'Id' || is_null($value) || strtolower($value) == 'null' )
				{
					unset($data[$key]); continue;
				}
			}
		}

		if ( $id != '' )
		{
			$data[$object->getEmptyIterator()->getIdAttribute()] = $id;
		}
		
		// validate field values
		$message = $this->validator_service->validate( $object, $data );
		
		if ( $message != '' ) throw new \Exception($message);
		
		// check record extists already
		$query = array();

		// remove client data
		unset($data['RecordCreated']);
		unset($data['RecordModified']);
		
		// convert data into database format
		$this->mapping_service->map($object, $data);

		// check an object exists already (search by Id or alternative key)
		$object_it = $object->getRegistry()->Query(
				$id != '' ? array(new \FilterInPredicate($id)) : $this->buildSearchQuery($object, $data)
		);
		
		if ( $object_it->getId() < 1 )
		{
			if ( !getFactory()->getAccessPolicy()->can_create($object) )
			{
				throw new \Exception('Lack of permissions to create object of '.get_class($object));
			}

			// convert to internal charset
			foreach( $data as $key => $value )
			{
				$data[$key] = \IteratorBase::utf8towin($value);
			}
			
			$result = $this->create($object, $data);
			
			if ( $result < 1 ) throw new \Exception('Unable create new record of '.get_class($object));
			
			return $this->get($entity, $result);
		}
		else
		{
			if ( !getFactory()->getAccessPolicy()->can_modify($object_it) )
			{
				throw new \Exception('Lack of permissions to modify object of '.get_class($object));
			}
			
			// convert to internal charset
			foreach( $data as $key => $value )
			{
				$data[$key] = \IteratorBase::utf8towin($value);
			}
			
			if ( $this->modify($object_it, $data) < 1 )
			{
				throw new \Exception('Unable update the record ('.$object_it->getId().') of '.get_class($object));
			}
			
			return $this->get($entity, $object_it->getId());
		}
	}
	
	public function get( $entity, $id = '', $output = 'text' )
	{
		if ( $id == '' ) $id = 0;
		
		$object = is_object($entity) ? $entity : $this->getObject($entity);
		
		$object_it = $object->getRegistry()->Query(
				array (
						new \FilterInPredicate($id),
						new \FilterVpdPredicate()
				)
		);
		
		if ( $object_it->getId() < 1 )
		{
			throw new \Exception('There is no record ('.$id.') of '.get_class($object));
		}

		return $this->sanitizeData($object_it->object, $object_it->getData(), $output);
	}
	
	public function delete( $entity, $id )
	{
		$object = is_object($entity) ? $entity : $this->getObject($entity);
		 
		$object_it = $object->getExact($id);
		
		if ( !getFactory()->getAccessPolicy()->can_delete($object_it) )
		{
			throw new \Exception('Lack of permissions to delete object of '.get_class($object_it->object));
		}
		
		$object_it->delete();
		
		return $this->sanitizeData(
				$object_it->object,
				$object_it->object->createCachedIterator()->getData()
		);
	}
	
	public function find( $entity, $limit = '', $offset = '')
	{
		$object = is_object($entity) ? $entity : $this->getObject($entity);
		
		$registry = $object->getRegistry();
		
		if ( $limit > 0 ) $registry->setLimit($limit);
		
		$query = array(
				new \FilterVpdPredicate()
		);

		// apply filters if any
		foreach($this->filter_resolver as $resolver )
		{ 
			$query = array_merge( $query, $resolver->resolve() );
		}
		
		$result = array();
		
		foreach( $registry->Query($query)->getRowset() as $row => $data )
		{
			$result[] = $this->sanitizeData($object, $data);
		}
		
		return $result;
	}
	
	static public function queryXPath( $object_it, $xpath )
	{
		$attributes = $object_it->object->getAttributes();
		
		$xml = '';
		while( !$object_it->end() )
		{
			$xml .= '<Object id="'.$object_it->getId().'">';
			foreach( $attributes as $attribute => $data )
			{	
				if ( $object_it->object->IsReference($attribute) ) {
					$value = $object_it->getRef($attribute)->getDisplayName();
				}
				else {
					$value = $object_it->getHtmlDecoded($attribute);
				}
				$xml .= '<'.$attribute.'><![CDATA['.\IteratorBase::wintoutf8(strtolower($value)).']]></'.$attribute.'>';
			}
			$xml .= '</Object>';
			$object_it->moveNext();
		}

		$ids = array();
		$xml_object = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><Collection>'.$xml.'</Collection>');
		foreach( $xml_object->xpath('/Collection/Object['.$xpath.']') as $item )
		{
			foreach( $item->attributes() as $attribute => $value ) {
				if ( $attribute == 'id' ) $ids[] = (string) $value; 
			}
		}
		
		$id_attribute = $object_it->object->getIdAttribute();
		
		return $object_it->object->createCachedIterator(
				array_filter( $object_it->getRowset(), function($row) use($id_attribute, $ids) {
						return in_array($row[$id_attribute], $ids);
				})
			);
	}
	
	protected function create( $object, $data )
	{
		$object_id = $object->add_parms($data);
		
		getFactory()->getEventsManager()
	    	->executeEventsAfterBusinessTransaction(
	    		$object->getExact($object_id), 'WorklfowMovementEventHandler');
		
	    return $object_id;
	}
	
	protected function modify( $object_it, $data )
	{
		return $object_it->object->modify_parms($object_it->getId(), $data);
	}
	
	protected function sanitizeData( $object, $data, $output = 'text' )
	{
		$id_attribute = $object->getIdAttribute();
		$system_attributes = $object->getAttributesByGroup('system');
		if ( is_a($object, 'MetaobjectStatable') ) $terminal_states = $object->getTerminalStates();
		
		$result = array();
		
		foreach( $data as $attribute => $value )
		{
			if ( is_numeric($attribute) || in_array($attribute, $system_attributes) ) continue;
			if ( $id_attribute == $attribute ) $attribute = "Id";
			
			if ( in_array($object->getAttributeType($attribute), array('datetime')) )
			{
				$result[$attribute] = \SystemDateTime::convertToClientTime($value);
			}
			else
			{
				$result[$attribute] = \IteratorBase::wintoutf8(
						html_entity_decode($value, ENT_QUOTES | ENT_HTML401, APP_ENCODING)
				);
				
				if ( $output == 'html' ) {
					$result[$attribute] = \IteratorBase::getHtmlValue($result[$attribute]);
				}
			}
			
			if ( $attribute == 'State' && is_array($terminal_states) )
			{
				$result['Completed'] = in_array($value, $terminal_states);
			}
			
			if ( $attribute == 'Attributes' )
			{
				// hard hack. make unique modified attributes for REST API /changes service
				$result[$attribute] = join(',',array_unique(preg_split('/,/', $result[$attribute])));
			}
		}
		
		unset($result['RecordVersion']);
		unset($result['Project']);
		unset($result['VPD']);
		
		return $result;
	}
	
	protected function getObject( $entity_name )
	{
		$class_name = getFactory()->getClass($entity_name);
		
		if ( $class_name == '' ) throw new \Exception('Unknown class name: '.$entity_name);
		
		return getFactory()->getObject($class_name);
	}
	
	protected function buildSearchQuery( $object, $data )
	{
		foreach( $data as $attribute => $value )
		{
			if ( $object->getAttributeDbType($attribute) == '' ) continue;
			if ( $object->getAttributeOrigin($attribute) == ORIGIN_CUSTOM ) continue;
			if ( !$object->IsAttributeStored($attribute) ) continue;
			
			if ( $attribute == "Description" ) continue;
			
			$predicate = new \FilterAttributePredicate($attribute, \IteratorBase::utf8towin($value));
			$predicate->setHasMultipleValues(false);
			
			$query[] = $predicate;
		}
		
		$query[] = new \FilterBaseVpdPredicate();
		
		return $query;
	}

	private $validator_service = null;
	
	private $mapping_service = null;
	
	private $filter_resolver = null;
}