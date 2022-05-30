<?php

class VersionedObjectRegistry extends ObjectRegistrySQL
{
	private $data = array();
	
	public function add( $class_name, $attributes )
	{
	    if ( !is_array($this->data[$class_name]) ) $this->data[$class_name] = array();
		$this->data[$class_name] = array_merge($this->data[$class_name], $attributes);
	}
	
	public function createSQLIterator( $sql )
	{
		foreach( getSession()->getBuilders('VersionedObjectRegistryBuilder') as $builder ) {
			$builder->build($this);
		}
		
		$data = array();
		foreach( $this->data as $key => $item ) {
			$data[] = array (
                'entityId' => $key,
                'ReferenceName' => $key,
                'Attributes' => $item
			);
		}
		
		return $this->createIterator($data);
	}
}