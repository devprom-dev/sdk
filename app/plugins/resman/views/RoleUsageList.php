<?php

class RoleUsageList extends ResourceList
{
 	var $role_it;
 	
 	function __construct( $object, $role_it )
 	{
 		parent::__construct( $object );
 		
 		$this->role_it = $role_it;
 	}
 	
 	function getPredicates( $values )
 	{
 		return array_merge( parent::getPredicates( $values ),
 				array (
			 			new ResourceRolePredicate($this->role_it->getId()),
			 			new ResourceUsageProjectPredicate( $this->getProjects() )
				)
 		);
 	}
}
