<?php

class BulkActionRegistry extends ObjectRegistrySQL
{
	function addModifyAction( $title, $attribute )
	{
		$this->actions['modify'][] = array (
				'name' => $title,
				'url' => 'Attribute'.$attribute
		);
	}
	
	function addAction( $title, $method )
	{
		$this->actions['modify'][] = array (
				'name' => $title,
				'url' => $method
		);
	}
	
	function addDeleteAction( $title, $parms )
	{
		$this->actions['delete'][] = array (
				'name' => $title,
				'url' => $parms
		);
	}

	function addWorkflowAction( $state, $title, $project, $parms )
	{
		$this->actions['workflow'][] = array (
				'state' => $state.'-'.$project,
				'name' => $title,
				'url' => 'Transition'.$parms.'&project='.$project
		);
	}
	
	function createSQLIterator( $sql )
 	{
 		foreach( getSession()->getBuilders('BulkActionBuilder') as $builder )
 		{
 			$builder->build($this);	
 		}
 		
 		$data = array();
 		foreach( $this->actions as $key => $actions ) {
 			foreach( $actions as $action ) {
	 			$data[] = array (
	 					'entityId' => $action['url'],
	 					'Caption' => $action['name'],
	 					'ReferenceName' => $action['state'],
	 					'package' => $key
	 			);
 			}
 		}
        return $this->createIterator( $data );
 	}

	private $actions = array();
}