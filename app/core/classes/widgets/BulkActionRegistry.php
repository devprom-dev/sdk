<?php

class BulkActionRegistry extends ObjectRegistrySQL
{
	function addModifyAction( $title, $attribute )
	{
		$this->actions['modify'][] = array (
				'name' => $title,
				'url' => 'Attribute'.$attribute
		);
		usort($this->actions['modify'], function( $left, $right ) {
			return $left['name'] > $right['name'];
		});
	}
	
	function addCustomAction( $title, $method )
	{
		$this->actions['action'][] = array (
			'name' => $title,
			'url' => $method
		);
	}

	function addActionUrl( $title, $url )
	{
		$this->actions['url'][] = array (
			'name' => $title,
			'url' => $url
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