<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class ObjectAccessWebMethod extends SelectWebMethod
{
 	var $access_it, $object_it;
 	
 	function execute_request()
 	{
 		global $_REQUEST;
 		
	 	if ( $_REQUEST['role'] != '' && $_REQUEST['class'] != '' && $_REQUEST['object'] != '' ) 
	 	{
	 		$this->execute($_REQUEST['role'], $_REQUEST['class'], $_REQUEST['object'], $_REQUEST['value'] );
	 	}
 	}
 	
 	function hasAccess()
 	{
 		$project_roles = getSession()->getRoles();
 		
 		return $project_roles['lead'];
 	}
 	
 	function drawSelect( $access_it, $object_it )
 	{
 		$this->access_it = $access_it;
 		$this->object_it = $object_it;

 		parent::drawSelect( 
			array( 
				'role' => $access_it->get('ProjectRole'),
				'class' => strtolower(get_class($object_it->object)),
				'object' => $object_it->getId()
				), 
			$access_it->get('AccessType') 
		);
 	}

 	function getStyle()
 	{
 		return 'width:100%;';
 	}
 	
 	function getValues()
 	{
 		global $model_factory;
 		
 		$policy = new AccessPolicyProject(getFactory()->getCacheService());

 		$policy->setRoles(array($this->access_it->get('ProjectRole')));
 		
        // check for default (role-based) permissions
		$access = $policy->getDefaultObjectAccess('read', $this->object_it);

 		return array (
 			'' => translate('По умолчанию').': '.($access ? 
 				translate('есть') : translate('нет')),
 			'view' => translate('Есть'),
 			'none' => translate('Нет')
 			);
 	}
 	
 	function execute ( $role_id, $classname, $object, $value )
 	{
 		$access = getFactory()->getObject('pm_ObjectAccess');
 		$access_it = $access->getByRefArray(
 			array (
 				'ProjectRole' => $role_id,
 				'ObjectClass' => $classname,
 				'ObjectId' => $object
 				)
 			);
 		
 		if ( $value == '' && $access_it->count() > 0 )
 		{
 			$access->delete($access_it->getId());
 		}
 		
 		if ( $value != '' )
 		{
 			if ( $access_it->count() < 1 )
 			{
	 			$access->add_parms(
		 			array (
		 				'ProjectRole' => $role_id,
		 				'ObjectClass' => $classname,
		 				'ObjectId' => $object,
		 				'AccessType' => $value
		 				)
	 			);
 			}
 			else
 			{
 				$access->modify_parms($access_it->getId(), array('AccessType' => $value));
 			}
 		}
 	}
}