<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once "AccessRightSelectWebMethod.php";

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
 		$access = getFactory()->getObject('pm_AccessRight');
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
 				$access->modify_parms($access_it->getId(), array('AccessType' => $value));
 			}
 		}
 	}
}