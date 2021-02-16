<?php

class KnowledgeBaseAccessPredicate extends FilterPredicate
{
 	function __construct() {
 		parent::__construct('access');
 	}
 	
 	function _predicate( $filter )
 	{
 	    if ( !is_object(getSession()) ) return " AND 1 = 1 ";
        if ( !is_object(getSession()->getUserIt()) ) return " AND 1 = 1 ";
        if ( getSession()->getUserIt()->getId() == '' ) return " AND 1 = 1 ";

		return  " AND NOT EXISTS (" .
				"	   SELECT a.VPD, a.ObjectId " .
				"		 FROM pm_ObjectAccess a, pm_ParticipantRole pr, pm_Participant p " .
				"	    WHERE INSTR(t.ParentPath, CONCAT(',',a.ObjectId,',')) > 0 " .
				"		  AND a.AccessType = 'none'" .
				"		  AND a.ObjectClass = 'projectpage' " .
				"		  AND a.ProjectRole = pr.ProjectRole " .
                "		  AND pr.Participant = p.pm_ParticipantId " .
                "		  AND p.SystemUser = " . getSession()->getUserIt()->getId().
                "		  AND a.VPD = p.VPD " .
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