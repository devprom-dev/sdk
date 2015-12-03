<?php

 class SourceCodeParticipantPersister extends ObjectSQLPersister
 {
 	function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT GROUP_CONCAT(CAST(u.SystemUser AS CHAR)) " .
			"     FROM pm_SubversionUser u " .
			"    WHERE u.Connector = t.Repository ".
 			"	   AND t.Author = u.UserName ) SystemUser "
 		);
 	}
 }
