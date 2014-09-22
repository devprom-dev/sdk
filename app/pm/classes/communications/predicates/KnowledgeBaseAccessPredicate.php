<?php

class KnowledgeBaseAccessPredicate extends FilterPredicate
{
 	function KnowledgeBaseAccessPredicate()
 	{
 		parent::FilterPredicate('access');
 	}
 	
 	function _predicate( $filter )
 	{
 		global $model_factory;

 		$project_roles = getSession()->getRoles();
 		
 		if( count($project_roles) < 1 ) return " AND 1 = 1 ";
 		
		return  " AND NOT EXISTS (" .
				"	   SELECT r.VPD, a.ObjectId " .
				"		 FROM pm_ProjectRole r, pm_ObjectAccess a " .
				"	    WHERE INSTR(t.ParentPath, CONCAT(',',a.ObjectId,',')) > 0 " .
				"	      AND a.ProjectRole = r.pm_ProjectRoleId" .
				"		  AND a.AccessType = 'none'" .
				"		  AND a.ObjectClass = 'projectpage' " .
				"		  AND r.ReferenceName IN ('".join("','", array_keys($project_roles))."')" .
				"         AND r.VPD IN ('".join("','",$this->getObject()->getVpds())."')" .
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