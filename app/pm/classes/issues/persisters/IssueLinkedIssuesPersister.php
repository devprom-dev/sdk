<?php

class IssueLinkedIssuesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =  
 			" CONCAT_WS(',',".
 			"     (SELECT GROUP_CONCAT(CAST(l.SourceRequest AS CHAR)) " .
			"        FROM pm_ChangeRequestLink l," .
            "             pm_ChangeRequestLinkType lkt" .
			"		WHERE l.LinkType = lkt.pm_ChangeRequestLinkTypeId " .
			"    	  AND l.TargetRequest = ".$this->getPK($alias)."), ".
 			"     (SELECT GROUP_CONCAT(CAST(l.TargetRequest AS CHAR)) " .
			"        FROM pm_ChangeRequestLink l," .
            "             pm_ChangeRequestLinkType lkt" .
			"		WHERE l.LinkType = lkt.pm_ChangeRequestLinkTypeId " .
			"    	  AND l.SourceRequest = ".$this->getPK($alias).") ) Links ";
 		
 		$columns[] =  
 			" CONCAT_WS(',',".
 			"     (SELECT GROUP_CONCAT(".
 			"				CONCAT_WS(':',".
 			"						  lkt.BackwardCaption,CAST(l.SourceRequest AS CHAR),".
 			"						  CASE lkt.ReferenceName WHEN 'blocks' THEN 'blocked' ELSE lkt.ReferenceName END,".
 			"						  sr.State,".
 			"						  CASE lkt.ReferenceName WHEN 'blocks' THEN 2 ELSE 1 END".
 			"			  )) " .
			"        FROM pm_ChangeRequestLink l," .
            "             pm_ChangeRequestLinkType lkt," .
            "			  pm_ChangeRequest sr ".
			"		WHERE l.LinkType = lkt.pm_ChangeRequestLinkTypeId " .
			"    	  AND l.TargetRequest = ".$this->getPK($alias).
			"		  AND l.SourceRequest = sr.pm_ChangeRequestId),  ".
 			"     (SELECT GROUP_CONCAT(CONCAT_WS(':',lkt.Caption,CAST(l.TargetRequest AS CHAR),lkt.ReferenceName,tr.State, 2)) " .
			"        FROM pm_ChangeRequestLink l," .
            "             pm_ChangeRequestLinkType lkt," .
            "			  pm_ChangeRequest tr ".
			"		WHERE l.LinkType = lkt.pm_ChangeRequestLinkTypeId " .
			"    	  AND l.SourceRequest = ".$this->getPK($alias).
 			"		  AND l.TargetRequest = tr.pm_ChangeRequestId) ) LinksWithTypes ";
 		
 		return $columns;
 	}
}
