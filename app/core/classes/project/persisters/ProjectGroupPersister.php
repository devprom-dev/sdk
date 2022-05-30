<?php

class ProjectGroupPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    $columns = array();
 	    
 		$columns[] =  
 			" (SELECT GROUP_CONCAT(CAST(l.ProjectGroup AS CHAR)) " .
			"    FROM co_ProjectGroupLink l " .
			"  	WHERE l.Project = ".$this->getPK($alias).") GroupId ";
 	    
 		return $columns;
 	}
}

