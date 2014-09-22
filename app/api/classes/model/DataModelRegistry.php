<?php

class DataModelRegistry extends ObjectRegistrySQL
{
	private $classes = array();
	
	public function addClass( $class_name )
	{
		if ( is_array($class_name) )
		{
			array_walk( $class_name, function(&$value, $index) {
				$value = strtolower($value);
			});
			
			$this->classes = array_merge($this->classes, $class_name); 
		}
		else
		{
			$this->classes[] = strtolower($class_name);
		}
	}
	
	public function getAll()
	{
		foreach( getSession()->getBuilders('IDataModelRegistryBuilder') as $builder )
		{
			$builder->build( $this );
		}
		
		$data = array();
		
		foreach( $this->classes as $class )
		{
			$data[] = array (
					'entityId' => $class,
					'Caption' => $class
			);
		}
		
		return $this->createIterator($data);
	}
}