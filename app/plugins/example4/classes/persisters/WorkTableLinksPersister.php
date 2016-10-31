<?php

class WorkTableLinksPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$sd_vpd = getFactory()->getObject('Project')->getByRef('CodeName', SERVICE_DESK_PROJECT)->get('VPD');
 		
 		if ( $sd_vpd == '' ) return $columns;
 		
 		$columns[] =  
 			" CONCAT_WS(',',".
 			"     (SELECT GROUP_CONCAT(CAST(l.SourceRequest AS CHAR)) " .
			"        FROM pm_ChangeRequestLink l," .
            "             pm_ChangeRequestLinkType lkt, " .
            "			  pm_ChangeRequest sr ".
            "		WHERE l.LinkType = lkt.pm_ChangeRequestLinkTypeId " .
			"    	  AND l.TargetRequest = ".$this->getPK($alias).
			"		  AND sr.pm_ChangeRequestId = l.SourceRequest ".
			"		  AND sr.VPD = '".$sd_vpd."' ".
			"	   ), ".
			"     (SELECT GROUP_CONCAT(CAST(l.TargetRequest AS CHAR)) " .
			"        FROM pm_ChangeRequestLink l," .
            "             pm_ChangeRequestLinkType lkt, " .
            "			  pm_ChangeRequest tr ".
			"		WHERE l.LinkType = lkt.pm_ChangeRequestLinkTypeId " .
			"    	  AND l.SourceRequest = ".$this->getPK($alias).
			"		  AND tr.pm_ChangeRequestId = l.TargetRequest ".
			"		  AND tr.VPD = '".$sd_vpd."' ".
 			"	   ) ) LinkedIssues ";
 		
 		return $columns;
 	}
}
