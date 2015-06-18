<?php

class ObjectsListWidgetRegistry extends ObjectRegistrySQL
{
	private $data = array();
	
	public function addReport( $class_name, $report_uid )
	{
		$this->data[] = array (
				'ReferenceName' => 'PMReport',
				'Caption' => $class_name,
				'entityId' => $report_uid
		);
	}
	
	public function addModule( $class_name, $module_uid )
	{
		$this->data[] = array (
				'ReferenceName' => 'Module',
				'Caption' => $class_name,
				'entityId' => $module_uid
		);
	}
	
 	function createSQLIterator( $sql )
 	{
 		foreach( getSession()->getBuilders('ObjectsListWidgetBuilder') as $builder ) {
 			$builder->build($this);
 		}
        return $this->createIterator( $this->data );
 	}
}