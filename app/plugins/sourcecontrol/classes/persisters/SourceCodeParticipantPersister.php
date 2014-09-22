<?php

 class SourceCodeParticipantPersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(p.pm_ParticipantId AS CHAR)) " .
			"     FROM pm_SubversionUser u, pm_Participant p " .
			"    WHERE p.VPD = u.VPD ".
 			"	   AND u.Connector = t.Repository ".
 			"	   AND t.Author = u.UserName ".
 			"      AND u.SystemUser = p.SystemUser ) Participant " 
 		);
 	}
 }
