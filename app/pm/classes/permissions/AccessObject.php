<?php

include "AccessObjectIterator.php";
include "AccessObjectRegistry.php";
include "persisters/ObjectAccessReferenceNamePersister.php";

class AccessObject extends Metaobject
{
 	function AccessObject( $registry = null ) 
 	{
 		parent::Metaobject('pm_ObjectAccess', $registry);
 		
 		$this->setSortDefault( array(
 		    new SortAttributeClause('ObjectId'),
 		    new SortAttributeClause('ProjectRole')
 		));
 		
 		$this->addPersister( new ObjectAccessReferenceNamePersister() );
 	}
 	
	function createIterator()
	{
		return new AccessObjectIterator( $this );
	}

	function getObjectAccessIt( $object_it )
	{
		if ( $object_it->getId() < 1 ) throw new Exception('Real object required, empty object is given');
		
		$sql = "  SELECT t.pm_ProjectRoleId ProjectRole, t.ProjectRoleBase, " .
			   "		(SELECT r.AccessType FROM pm_ObjectAccess r " .
			   "		  WHERE r.ObjectClass = '".strtolower(get_class($object_it->object))."'" .
			   "			AND r.ObjectId = " .$object_it->getId().
			   "			AND r.ProjectRole = t.pm_ProjectRoleId) AccessType, " .
			   "		(SELECT r.ObjectId FROM pm_ObjectAccess r " .
			   "		  WHERE r.ObjectClass = '".strtolower(get_class($object_it->object))."'" .
			   "			AND r.ObjectId = " .$object_it->getId().
			   "			AND r.ProjectRole = t.pm_ProjectRoleId) ObjectId " .
			   "   FROM pm_ProjectRole t" .
			   "  WHERE t.VPD IN ('".join("','",$this->getVpds())."')";
			   
		return $this->createSQLIterator( $sql );
	}
	
	function getClassesIt()
	{
		$sql = " SELECT DISTINCT t.ObjectClass" .
			   "   FROM pm_ObjectAccess t" .
			   "  WHERE 1 = 1 ".$this->getVpdPredicate();
  
		return $this->createSQLIterator( $sql );
	}
}
