<?php

include "ProjectRoleIterator.php";
include "predicates/ProjectRoleInheritedFilter.php";

class ProjectRole extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('pm_ProjectRole');
 		
 		$this->setSortDefault( new SortAttributeClause('Caption') );
 		
 		$this->setAttributeType('ProjectRoleBase', 'REF_ProjectRoleBaseId');
 		
 		$this->setAttributeVisible('ProjectRoleBase', true);
 		$this->setAttributeVisible('ReferenceName', false);

 		$this->addAttributeGroup('ReferenceName', 'system');
 	}

 	function createIterator()
 	{
 		return new ProjectRoleIterator( $this );
 	}

  	function IsVPDEnabled()
 	{
 		return true;
 	}
 	
 	function add_parms( $parms )
	{
		global $model_factory;
		
		if ( $parms['ReferenceName'] == '' )
		{
			$base = $model_factory->getObject('ProjectRoleBase');
			$base_it = $base->getExact($parms['ProjectRoleBase']);

			$parms['ReferenceName'] = $base_it->get('ReferenceName');
		}
		
		if ( $parms['ProjectRoleBase'] == '' )
		{
			$base = $model_factory->getObject('ProjectRoleBase');
			$base_it = $base->getByRef('ReferenceName', $parms['ReferenceName']);

			$parms['ProjectRoleBase'] = $base_it->getId();
		}

		return parent::add_parms( $parms );
	}

	function modify_parms( $id, $parms )
	{
		global $model_factory;
		
		if ( $parms['ProjectRoleBase'] != '' )
		{
			$base = $model_factory->getObject('ProjectRoleBase');
			$base_it = $base->getExact($parms['ProjectRoleBase']);
	
			$parms['ReferenceName'] = $base_it->get('ReferenceName');
		}

		return parent::modify_parms( $id, $parms );
	}
	
	function IsDeletedCascade( $object )
	{
		return false;
	}
	
	function DeletesCascade( $object )
	{
		return false;
	}
}