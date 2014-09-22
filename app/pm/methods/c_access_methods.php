<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

 ///////////////////////////////////////////////////////////////////////////////////////
 class AccessRightSelectWebMethod extends SelectWebMethod
 {
 	var $access_it;
 	
 	function execute_request()
 	{
 		global $_REQUEST;
 		
	 	if ( $_REQUEST['role'] != '' && $_REQUEST['object'] != '' && $_REQUEST['kind'] != '' ) 
	 	{
	 		$this->execute($_REQUEST['role'], $_REQUEST['object'], $_REQUEST['kind'], $_REQUEST['value']);
	 	}
 	}
 	
 	function drawSelect( $access_it, $access_type )
 	{
 		$this->access_it = $access_it;
 		
 		parent::drawSelect( 
			array( 
				'role' => $access_it->get('ProjectRole'),
				'object' => $access_it->get('ReferenceName'),
				'kind' => $access_it->get('ReferenceType')
				), 
			$access_type
		);
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class StoreAccessWebMethod extends AccessRightSelectWebMethod
 {
 	function hasAccess()
 	{
 		return getFactory()->getAccessPolicy()->can_modify(getFactory()->getObject('pm_AccessRight'));
 	}
 	
 	function getStyle()
 	{
 		return 'width:100%;';
 	}
 	
 	function getValues()
 	{
 		global $model_factory;
 		
 		$policy = new AccessPolicyProject(getFactory()->getCacheService());

 		$policy->setRoles(
 				array(
 						$this->access_it->get('ProjectRole')
 				)
 			);

 		switch ( $this->access_it->get('ReferenceType') )
 		{
 			case 'PMReport':
 			    
 			    $page = $model_factory->getObject($this->access_it->get('ReferenceType'));
				
 			    $page_it = $page->getExact( $this->access_it->get('ReferenceName') );

 				$access = $policy->getDefaultObjectAccess('read', $page_it);

		 		return array (
		 			'' => translate('По умолчанию').': '.($access ? 
		 				translate('есть') : translate('нет')),
		 			'view' => translate('Есть'),
		 			'none' => translate('Нет')
		 			);
 			    
 			case 'PMPluginModule':

 			    $page = $model_factory->getObject('Module');
				
 			    $page_it = $page->getExact( $this->access_it->get('ReferenceName') );

 				$access = $policy->getDefaultObjectAccess('read', $page_it);

		 		return array (
		 			'' => translate('По умолчанию').': '.($access ? 
		 				translate('есть') : translate('нет')),
		 			'view' => translate('Есть'),
		 			'none' => translate('Нет')
		 			);

 			case 'Y':
 				$object = $model_factory->getObject($this->access_it->get('ReferenceName'));

 				$read_access = $policy->getDefaultEntityAccess(ACCESS_READ, $object);
 				
 				if ( $read_access )
 				{
	 				$modify_access = $policy->getDefaultEntityAccess(ACCESS_MODIFY, $object);
 				}

		 		return array (
		 			'' => translate('По умолчанию').': '.($read_access ? 
		 				($modify_access ? translate('изменение') : translate('просмотр')) : translate('нет')),
		 			'modify' => translate('Изменение'),
		 			'view' => translate('Просмотр'),
		 			'none' => translate('Нет')
		 			);

 			case 'A':
 				$parts = preg_split('/\./', $this->access_it->get('ReferenceName'));
 				$object = $model_factory->getObject($parts[0]);

 				$read_access = true;
 				
 				if ( $read_access )
 				{
	 				$modify_access = true;
 				}

		 		$values = array (
		 			'' => translate('По умолчанию').': '.($read_access ? 
		 				($modify_access ? translate('изменение') : translate('просмотр')) : translate('нет')),
		 			'modify' => translate('Изменение'),
		 			'view' => translate('Просмотр')
		 		);
		 		
		 		if ( !$object->IsReference($parts[1]) )
		 		{
		 		   $values['none'] = translate('Нет');
		 		}		 			 
		 			
		 	    return $values;
 		}
 	}
 	
 	function execute ( $role_id, $object, $kind, $value )
 	{
 		global $model_factory;
 		
 		$access = $model_factory->getObject('pm_AccessRight');
 		$access_it = $access->getByRefArray(
 			array (
 				'ProjectRole' => $role_id,
 				'ReferenceName' => $object,
 				'ReferenceType' => $kind
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
		 				'ReferenceName' => $object,
		 				'ReferenceType' => $kind,
		 				'AccessType' => $value
		 				)
	 			);
 			}
 			else
 			{
 				$access_it->modify( array('AccessType' => $value) );
 			}
 		}
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
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
 		global $model_factory;
 		
 		$access = $model_factory->getObject('pm_ObjectAccess');
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
 				$access_it->modify( array('AccessType' => $value) );
 			}
 		}
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class AccessObjectFilterViewWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return text(757);
 	}

 	function getValues()
 	{
  		return array (
 			'all' => translate('Все'),
 			'module' => translate('Модули'),
 			'report' => translate('Отчеты'),
 			'entity' => translate('Сущности'),
 			'attribute' => translate('Атрибуты'),
  			'object' => translate('Объекты')
 			);
	}
	
	function getStyle()
	{
		return 'width:200px;';
	}
	
	function getValueParm()
	{
		return 'object';
	}
	
	function getType()
	{
		return 'singlevalue';
	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class AccessClassFilterViewWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Сущность');
 	}

 	function getValues()
 	{
 		global $model_factory;
 		
 		$values = array();
 		
 		$access = $model_factory->getObject('pm_ObjectAccess');
 		$class_it = $access->getClassesIt();
 		
 		$values['all'] = translate('Все');
 		
 		while ( !$class_it->end() )
 		{
 			$object = $model_factory->getObject($class_it->get('ObjectClass'));
 			
 			$values[$class_it->get('ObjectClass')] = $object->getDisplayName();
 			$class_it->moveNext();
 		}
 		
  		return $values;
	}
	
	function getStyle()
	{
		return 'width:200px;';
	}
	
	function getValueParm()
	{
		return 'class';
	}
	
	function getType()
	{
		return 'singlevalue';
	}
 }

?>