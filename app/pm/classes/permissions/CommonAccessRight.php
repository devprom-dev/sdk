<?php

include "CommonAccessRightIterator.php";

include "predicates/CommonAccessClassPredicate.php";
include "predicates/CommonAccessEntityPredicate.php";
include "predicates/CommonAccessObjectPredicate.php";
include "predicates/CommonAccessReportPredicate.php";
include "predicates/CommonAccessRolePredicate.php";

class CommonAccessRight extends Metaobject
{
 	var $objects;
 	
 	function CommonAccessRight()
 	{
 		global $model_factory;
 		
 		$this->objects = array();
 		
 		parent::Metaobject('pm_AccessRight');
 		
 		$this->addObject( $model_factory->getObject('ProjectPage') );
 	}
 	
	function createIterator()
	{
		return new CommonAccessRightIterator( $this );
	}

	function addObject( & $object )
	{
		$this->objects[] = $object;
	}
	
	function getFilterValue( $filter_class )
	{
		foreach( $this->getFilters() as $filter )
		{
			if ( is_a($filter, $filter_class) )
			{
				return $filter->getValue();
			}
		}
		
		return '';
	}
	
	function getDataEntities()
	{
	    foreach( getSession()->getBuilders('AccessRightEntitySetBuilder') as $builder )
	    {
	        $builder->build( $this );
	    }
	    
 		foreach( $this->objects as $object )
 		{
 			$data[] = array (
 				'ReferenceName' => get_class($object) == 'Metaobject' ? $object->getClassName() : strtolower(get_class($object)),
 				'ReferenceType' => 'Y',
 				'DisplayName' => $object->getDisplayName()
 			);
 		}
 		
 		usort( $data, 'usort_display_name' );
 		
 		return $data;
	}
	
	function getDataAccess()
	{
		global $model_factory;
		
		$data = array();
		
		$object_class = $this->getFilterValue( 'CommonAccessClassPredicate' );
		
 		$access = $model_factory->getObject('pm_ObjectAccess');
 		$access_it = $access->getAll();

 		while( !$access_it->end() )
 		{
 			if ( !in_array($object_class, array('', 'all', strtolower($access_it->get('ObjectClass')) )) )
 			{
 				$access_it->moveNext();
 				continue;
 			}
 			
 			$data[] = array (
 				'ReferenceName' => $access_it->get('ObjectClass').'.'.$access_it->get('ObjectId'),
 				'ReferenceType' => 'O',
 				'DisplayName' => $access_it->get('ObjectClass')
 			);
 			
 			$access_it->moveNext();
 		}
		
 		usort( $data, 'usort_display_name' );
 			
 		return $data;
	}
	
	function getDataModules()
	{
		$data = array();

		$area_set = new FunctionalArea();
		
		$module_it = getFactory()->getObject('Module')->getAll();
		
		while ( !$module_it->end() )
		{
		    $title = $area_set->getExact($module_it->get('Area'))->getDisplayName();
		    
		    if ( $title == '' )
		    {
		    	$module_it->moveNext(); continue;
		    }
		    
		    if ( $module_it->getDisplayName() != '' ) $title .= ': '.$module_it->getDisplayName();
		    
 			$data[] = array (
 				'ReferenceName' => $module_it->getId(),
 				'ReferenceType' => 'PMPluginModule',
 				'DisplayName' => $title
 			);

 			$module_it->moveNext();
		}
 		
		usort( $data, 'usort_display_name' );
		
		return $data;
	}
	
	function getDataReports()
	{
		$data = array();
		
		$report_filter = $this->getFilterValue( 'CommonAccessReportPredicate' );
		
		$report_it = getFactory()->getObject('PMReport')->getAll();
		
		while ( !$report_it->end() )
		{
 			if ( !in_array($report_filter, array('', 'all', $report_it->getId() )) )
 			{
 				$report_it->moveNext();
 				continue;
 			}
			
		 	if ( is_numeric($report_it->getId()) )
 			{
 				$report_it->moveNext();
 				continue;
 			}
 			
 			$data[] = array (
 				'ReferenceName' => $report_it->getId(),
 				'ReferenceType' => 'PMReport',
 				'DisplayName' => $report_it->getDisplayName()
 			);

			$report_it->moveNext();
		}
		
		usort( $data, 'usort_display_name' );
		
		return $data;
	}
	
	function getDataAttributes()
	{
		global $model_factory;
		
		$data = array();

		$entity_filter = $this->getFilterValue( 'CommonAccessEntityPredicate' );
		
		$entity = getFactory()->getObject('AttributePermissionEntity');
		
		$object_it = $entity->getAll();
		
		while( !$object_it->end() )
		{
			$object = $model_factory->getObject($object_it->getId());

			if ( !in_array($entity_filter, array('', 'all', $object_it->getId() )) )
 			{
 				continue;
 			}			
			
			$attributes = $object->getAttributesByGroup('permissions');
				    
			foreach( $attributes as $attribute )
			{
				if ( $object->getAttributeType($attribute) == '' ) continue;
				
	 			$data[] = array (
	 				'ReferenceName' => get_class($object).'.'.$attribute,
	 				'ReferenceType' => 'A',
	 				'DisplayName' => $object->getDisplayName().'.'.translate($object->getAttributeUserName($attribute))
	 			);
			}
			
			$object_it->moveNext();
		}
		
		usort( $data, 'usort_display_name' );
		
		return $data;
	}
	
	function getData()
	{
		global $model_factory;

		$filter_kind = $this->getFilterValue( 'CommonAccessObjectPredicate' );  
		
		$data = array();
		
		if ( in_array($filter_kind, array('', 'all', 'entity')) )
		{
			$data = array_merge($data, $this->getDataEntities());	
		}
		
		if ( in_array($filter_kind, array('', 'all', 'object')) )
		{
			$data = array_merge($data, $this->getDataAccess());	
		}
		
		if ( in_array($filter_kind, array('', 'all', 'module')) )
		{
			$data = array_merge($data, $this->getDataModules());	
		}

		if ( in_array($filter_kind, array('', 'all', 'report')) )
		{
			$data = array_merge($data, $this->getDataReports());	
		}
		
		if ( in_array($filter_kind, array('', 'all', 'attribute')) )
		{
			$data = array_merge($data, $this->getDataAttributes());	
		}

		$role = $model_factory->getObject('pm_ProjectRole');
		$role_it = $role->getAll();

		$filter_role = $this->getFilterValue( 'CommonAccessRolePredicate' );
		
		$recordset = array();
		
		while( !$role_it->end() )
		{
 			if ( !in_array($filter_role, array('', 'all', $role_it->getId() )) )
 			{
 				$role_it->moveNext();
 				continue;
 			}
			
 			$role_name = $role_it->getDisplayName();
 			
 			foreach( $data as $key => $value )
 			{
 				$data[$key]['ProjectRole'] = $role_it->getId();
 				$data[$key]['ProjectRoleName'] = $role_name;
 			}
 			
 			$recordset = array_merge( $recordset, $data );
 			
			$role_it->moveNext();
		}
		
		foreach ( $this->getRegistryDefault()->getSorts() as $sort )
		{
			if ( $sort instanceof SortAttributeClause && $sort->getAttributeName() == 'ReferenceName' )
			{
				usort( $recordset, 'usort_display_name' );
			}
		}
		
		return $recordset;
	}
	
	function getAll()
	{
		$data = $this->getData();
		
		return $this->createCachedIterator( $data );
	}
}
 
function usort_display_name( $left, $right )
{
	if ( $left['DisplayName'] == $right['DisplayName'] )
	{
		return $left['ProjectRoleName'] > $right['ProjectRoleName'] ? 1 : -1;
	}
	else
	{
		return $left['DisplayName'] > $right['DisplayName'] ? 1 : -1;
	}
}