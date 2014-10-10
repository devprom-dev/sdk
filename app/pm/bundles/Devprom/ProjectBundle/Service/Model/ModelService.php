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

		// convert to internal charset
		foreach( $data as $key => $value )
		{
			if ( $key == 'Id' || is_null($value) || strtolower($value) == 'null' )
			{
				unset($data[$key]); continue;
			}
			
			$data[$key] = \IteratorBase::utf8towin($value);
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

		// check an object exists already
		if ( $id != '' )
		{
			$query[] = new \FilterInPredicate($id);
		}
		else
		{
			foreach( $data as $attribute => $value )
			{
				if ( $object->getAttributeDbType($attribute) == '' ) continue;
				if ( $object->getAttributeOrigin($attribute) == ORIGIN_CUSTOM ) continue;
				if ( $attribute == "Description" ) continue;
				
				$predicate = new \FilterAttributePredicate($attribute, $value);
				
				$predicate->setHasMultipleValues(false);
				
				$query[] = $predicate;
			}
		}
		
		$object_it = $object->getRegistry()->Query($query);
		
		if ( $object_it->getId() < 1 )
		{
			if ( !getFactory()->getAccessPolicy()->can_create($object) )
			{
				throw new \Exception('Lack of permissions to create object of '.get_class($object));
			}
			
			$result = $object->add_parms($data);
			
			if ( $result < 1 ) throw new \Exception('Unable create new record of '.get_class($object));
			
			return $this->get($entity, $result);
		}
		else
		{
			if ( !getFactory()->getAccessPolicy()->can_modify($object_it) )
			{
				throw new \Exception('Lack of permissions to modify object of '.get_class($object));
			}
			
			if ( $object_it->modify($data) < 1 )
			{
				throw new \Exception('Unable update the record ('.$object_it->getId().') of '.get_class($object));
			}
			
			return $this->get($entity, $object_it->getId());
		}
	}
	
	public function get( $entity, $id = '' )
	{
		if ( $id == '' ) $id = 0;
		
		$object = is_object($entity) ? $entity : $this->getObject($entity);
		
		$object_it = $object->getRegistry()->Query(
				array (
						new \FilterInPredicate($id),
						new \FilterVpdPredicate($object->getVpds())
				)
		);
		
		if ( $object_it->getId() < 1 )
		{
			throw new \Exception('There is no record ('.$id.') of '.get_class($object));
		}

		return $this->sanitizeData($object_it->object, $object_it->getData());
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
				new \FilterVpdPredicate($object->getVpds())
		);

		// apply filters if any
		if ( is_object($this->filter_resolver) )
		{
			$query = array_merge( $query, $this->filter_resolver->resolve() );
		}
		
		$result = array();
		
		foreach( $registry->Query($query)->getRowset() as $row => $data )
		{
			$result[] = $this->sanitizeData($object, $data);
		}
		
		return $result;
	}
	
	protected function sanitizeData( $object, $data )
	{
		$id_attribute = $object->getIdAttribute();
		
		$system_attributes = $object->getAttributesByGroup('system');
		
		$result = array();
		
		foreach( $data as $attribute => $value )
		{
			if ( is_numeric($attribute) || in_array($attribute, $system_attributes) ) continue;
			
			if ( $id_attribute == $attribute ) $attribute = "Id";
			
			$result[$attribute] = \IteratorBase::wintoutf8(
					html_entity_decode($value, ENT_COMPAT | ENT_HTML401, 'cp1251')
			);
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

	private $validator_service = null;
	
	private $mapping_service = null;
	
	private $filter_resolver = null;
}