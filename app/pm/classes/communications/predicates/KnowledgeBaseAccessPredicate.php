<?php

class KnowledgeBaseAccessPredicate extends FilterPredicate
{
 	function __construct() {
 		parent::__construct('access');
 	}
 	
 	function _predicate( $filter )
 	{
 		$project_roles = getSession()->getParticipantIt()->getRoles();
 		if( count($project_roles) < 1 ) return " AND 1 = 1 ";
 		
		return  " AND NOT EXISTS (" .
				"	   SELECT a.VPD, a.ObjectId " .
				"		 FROM pm_ObjectAccess a " .
				"	    WHERE INSTR(t.ParentPath, CONCAT(',',a.ObjectId,',')) > 0 " .
				"		  AND a.AccessType = 'none'" .
				"		  AND a.ObjectClass = 'projectpage' " .
				"		  AND a.ProjectRole IN ('".join("','", array_values($project_roles))."')" .
				"         AND a.VPD IN ('".join("','",$this->getObject()->getVpds())."')" .
				" 	  )" .
				" AND NOT EXISTS (" .
				"	   SELECT r.VPD, a.ObjectId " .
				"		 FROM pm_ProjectRole r, pm_ObjectAccess a " .
				"	    WHERE a.ObjectId = t.WikiPageId" .
				"	      AND a.ProjectRole = r.pm_ProjectRoleId" .
				"		  AND a.ObjectClass = 'projectpage'" .
				"		  AND a.AccessType = 'none'" .
				"		  AND r.ReferenceName = 'linkedguest'" .
				"         AND r.VPD NOT IN ('".join("','",$this->getObject()->getVpds())."')" .
				"	  )";
 	}
} 