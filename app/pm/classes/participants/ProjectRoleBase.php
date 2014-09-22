<?php

include "ProjectRoleBaseIterator.php";
include "ProjectRoleBaseRegistry.php";

class ProjectRoleBase extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('pm_ProjectRole', new ProjectRoleBaseRegistry($this));
 		
 		$this->setSortDefault( new SortAttributeClause('Caption') );
 		
 		$this->setAttributeType('ProjectRoleBase', 'REF_ProjectRoleBaseId');
 		
 		$this->setAttributeRequired('ReferenceName', true);
 		
 		$this->setAttributeRequired('ProjectRoleBase', false);
 	}
 	
 	function createIterator()
 	{
 		return new ProjectRoleBaseIterator( $this );
 	}
 	
 	function IsVPDEnabled()
 	{
 		return false;
 	}

 	function getPlayedByUser( $user_it )
 	{
 		$sql = " SELECT r.* " .
 			   "   FROM pm_ProjectRole r" .
 			   "  WHERE r.VPD IS NULL" .
 			   "	AND EXISTS (SELECT ppr.ProjectRoleBase " .
 			   "				  FROM pm_Participant p," .
 			   "					   pm_ParticipantRole pr," .
 			   "					   pm_ProjectRole ppr" .
 			   "  				 WHERE p.SystemUser = ".$user_it->getId().
 			   "    			   AND p.pm_ParticipantId = pr.Participant" .
 			   "				   AND pr.ProjectRole = ppr.pm_ProjectRoleId" .
 			   "				   AND ppr.ProjectRoleBase = r.pm_ProjectRoleId )";

		return $this->createSQLIterator( $sql );
 	}
}