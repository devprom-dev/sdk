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
 			"     (SELECT GROUP_CONCAT(CONCAT_WS(':',lkt.BackwardCaption,CAST(l.SourceRequest AS CHAR))) " .
			"        FROM pm_ChangeRequestLink l," .
            "             pm_ChangeRequestLinkType lkt" .
			"		WHERE l.LinkType = lkt.pm_ChangeRequestLinkTypeId " .
			"    	  AND l.TargetRequest = ".$this->getPK($alias)."), ".
 			"     (SELECT GROUP_CONCAT(CONCAT_WS(':',lkt.Caption,CAST(l.TargetRequest AS CHAR))) " .
			"        FROM pm_ChangeRequestLink l," .
            "             pm_ChangeRequestLinkType lkt" .
			"		WHERE l.LinkType = lkt.pm_ChangeRequestLinkTypeId " .
			"    	  AND l.SourceRequest = ".$this->getPK($alias).") ) LinksWithTypes ";
 		
 		return $columns;
 	}
}
