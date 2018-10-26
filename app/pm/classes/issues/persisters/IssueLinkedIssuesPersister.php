<?php

class IssueLinkedIssuesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =  
 			" CONCAT_WS(',',".
 			"     (SELECT GROUP_CONCAT(CAST(l.SourceRequest AS CHAR)) " .
			"        FROM pm_ChangeRequestLink l " .
			"		WHERE l.TargetRequest = ".$this->getPK($alias)."), ".
 			"     (SELECT GROUP_CONCAT(CAST(l.TargetRequest AS CHAR)) " .
			"        FROM pm_ChangeRequestLink l " .
			"		WHERE l.SourceRequest = ".$this->getPK($alias).") ) Links ";
 		
 		$columns[] =  
 			" CONCAT_WS(',',".
 			"     (SELECT GROUP_CONCAT(".
 			"				CONCAT_WS(':',".
 			"						  lkt.BackwardCaption,CAST(l.SourceRequest AS CHAR),".
 			"						  CASE lkt.ReferenceName WHEN 'blocks' THEN 'blocked' ELSE lkt.ReferenceName END,".
 			"						  IFNULL(st.IsTerminal, 'N'),".
 			"						  CASE lkt.ReferenceName WHEN 'blocks' THEN 2 ELSE 1 END".
 			"			  )) " .
			"        FROM pm_ChangeRequestLink l," .
            "             pm_ChangeRequestLinkType lkt," .
            "			  pm_ChangeRequest sr LEFT OUTER JOIN pm_StateObject so ON sr.StateObject = so.pm_StateObjectId ".
            "			        LEFT OUTER JOIN pm_State st ON so.State = st.pm_StateId ".
			"		WHERE l.LinkType = lkt.pm_ChangeRequestLinkTypeId " .
			"    	  AND l.TargetRequest = ".$this->getPK($alias).
			"		  AND l.SourceRequest = sr.pm_ChangeRequestId ), ".
 			"     (SELECT GROUP_CONCAT(CONCAT_WS(':',lkt.Caption,CAST(l.TargetRequest AS CHAR),lkt.ReferenceName,IFNULL(st.IsTerminal,'N'), 2)) " .
			"        FROM pm_ChangeRequestLink l," .
            "             pm_ChangeRequestLinkType lkt," .
            "			  pm_ChangeRequest tr LEFT OUTER JOIN pm_StateObject so ON tr.StateObject = so.pm_StateObjectId ".
            "			        LEFT OUTER JOIN pm_State st ON so.State = st.pm_StateId ".
			"		WHERE l.LinkType = lkt.pm_ChangeRequestLinkTypeId " .
			"    	  AND l.SourceRequest = ".$this->getPK($alias).
 			"		  AND l.TargetRequest = tr.pm_ChangeRequestId ".
            "   ) ) LinksWithTypes ";
 		
 		return $columns;
 	}
}
