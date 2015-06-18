<?php
 
if ( !class_exists('ResourceList', false) ) include "ResourceList.php";

class UserUsageList extends ResourceList
{
 	var $user_it;
 	
 	function __construct( $object, $scale )
 	{
 		global $model_factory, $_REQUEST;
 		
 		$user = $model_factory->getObject('cms_User');
 		$this->user_it = $user->getExact( $_REQUEST['user'] );
 		 
 		parent::__construct( $object, $scale );
 	}
 	
 	function getPredicates( $values )
 	{
 		return array_merge( parent::getPredicates( $values ),
 				array (
			 			new ResourceUsageProjectPredicate( $this->getProjects() ),
			 			new ResourceUserPredicate($this->user_it->getId()),
			 			new ResourceRolePredicate($values['role'])
 				)
 		);
 	}
}
