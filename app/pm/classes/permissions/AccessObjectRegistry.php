<?php

class AccessObjectRegistry extends ObjectRegistrySQL
{
	private $object_it = null;
	
	public function setObjectIt( $object_it )
	{
		$this->object_it = $object_it;
	}
	
	public function getObjectIt()
	{
		return $this->object_it;
	}
	
	function getAll()
	{
		if ( $this->object_it->getId() < 1 ) throw new Exception('Real object required, empty object is given');
		
		$sql = "  SELECT t.pm_ProjectRoleId ProjectRole, t.ProjectRoleBase, " .
			   "		(SELECT r.AccessType FROM pm_ObjectAccess r " .
			   "		  WHERE r.ObjectClass = '".strtolower(get_class($this->object_it->object))."'" .
			   "			AND r.ObjectId = " .$this->object_it->getId().
			   "			AND r.ProjectRole = t.pm_ProjectRoleId) AccessType, " .
			   "		(SELECT r.ObjectId FROM pm_ObjectAccess r " .
			   "		  WHERE r.ObjectClass = '".strtolower(get_class($this->object_it->object))."'" .
			   "			AND r.ObjectId = " .$this->object_it->getId().
			   "			AND r.ProjectRole = t.pm_ProjectRoleId) ObjectId " .
			   "   FROM pm_ProjectRole t" .
			   "  WHERE t.VPD IN ('".join("','",$this->getObject()->getVpds())."')";

		return $this->createSQLIterator( $sql );
	}
}