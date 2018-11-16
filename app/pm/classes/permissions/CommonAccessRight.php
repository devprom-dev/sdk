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
        $entities = array_filter(
            preg_split('/,/', $this->getFilterValue( 'CommonAccessEntityPredicate' )),
	        function($value) {
                return $value != '';
            }
        );

	    foreach( getSession()->getBuilders('AccessRightEntitySetBuilder') as $builder ) {
	        $builder->build( $this );
	    }

 		foreach( $this->objects as $object )
 		{
 		    $className = get_class($object) == 'Metaobject' ? $object->getClassName() : strtolower(get_class($object));
 		    if ( count($entities) > 0 && !in_array($className, $entities) ) continue;

 			$data[] = array (
 				'ReferenceName' => $className,
 				'ReferenceType' => 'Y',
 				'DisplayName' => $object->getDisplayName()
 			);
 		}

 		usort( $data, 'usort_display_name' );
 		
 		return $data;
	}
	
	function getDataAccess()
	{
		$data = array();

		$object_class = $this->getFilterValue( 'CommonAccessClassPredicate' );
		$role_filter = $this->getFilterValue( 'CommonAccessRolePredicate' );

		$access_it = getFactory()->getObject('pm_ObjectAccess')->getRegistry()->Query(
				array (
					new FilterBaseVpdPredicate(),
					new FilterAttributePredicate('ObjectClass', $object_class),
					new FilterAttributePredicate('ProjectRole', $role_filter)
				)
		);

 		while( !$access_it->end() )
 		{
 			$data[] = array (
 				'ReferenceName' => $access_it->get('ObjectClass').'.'.$access_it->get('ObjectId'),
 				'ReferenceType' => 'O',
 				'DisplayName' => $access_it->get('ObjectClass'),
				'ProjectRole' => $access_it->get('ProjectRole'),
				'ProjectRoleName' => $access_it->get('ProjectRoleName')
 			);
 			$access_it->moveNext();
 		}
		
 		usort( $data, 'usort_display_name' );

 		return $data;
	}
	
	function getDataModules()
	{
		$data = array();

        if ( $this->getFilterValue( 'CommonAccessEntityPredicate' ) != '' ) return $data;

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

        if ( $this->getFilterValue( 'CommonAccessEntityPredicate' ) != '' ) return $data;

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
		$data = array();
		$entity_filter = $this->getFilterValue( 'CommonAccessEntityPredicate' );
		
		$object_it = getFactory()->getObject('AttributePermissionEntity')->getAll();
		while( !$object_it->end() )
		{
			if ( !in_array($entity_filter, array('', 'all', $object_it->getId() )) ) {
                $object_it->moveNext();
			    continue;
            }

			$object = getFactory()->getObject($object_it->getId());

			$attributes = $object->getAttributesByGroup('permissions');
			foreach( $attributes as $attribute )
			{
	 			$data[] = array (
	 				'ReferenceName' => get_class($object).'.'.$attribute,
	 				'ReferenceType' => 'A',
	 				'DisplayName' => $object_it->get('Caption').'.'.translate($object->getAttributeUserName($attribute))
	 			);
			}
			foreach( $object->getAttributesRemoved() as $attribute => $info )
			{
				$data[] = array (
						'ReferenceName' => get_class($object).'.'.$attribute,
						'ReferenceType' => 'A',
						'DisplayName' => $object_it->get('Caption').'.'.translate($info['caption'])
				);
			}

			$object_it->moveNext();
		}
		
		usort( $data, 'usort_display_name' );
		
		return $data;
	}
	
	function getData()
	{
		$filter_kind = $this->getFilterValue( 'CommonAccessObjectPredicate' );
		
		$data = array();
		
		if ( in_array($filter_kind, array('', 'all', 'entity')) )
		{
			$data = array_merge($data, $this->getDataEntities());	
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

		$role = getFactory()->getObject('pm_ProjectRole');
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

		if ( in_array($filter_kind, array('', 'all', 'object')) )
		{
			$recordset = array_merge($recordset, $this->getDataAccess());
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