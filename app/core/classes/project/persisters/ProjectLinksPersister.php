<?php

class ProjectLinksPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    $columns = array();
 	    
 		$columns[] =  
 			   " CONCAT_WS(',',".
 			   "	(SELECT GROUP_CONCAT(CAST(l.Target AS CHAR)) FROM pm_ProjectLink l WHERE l.Source = t.pm_ProjectId AND l.LinkType = 1), ".
 			   "	(SELECT GROUP_CONCAT(CAST(l.Source AS CHAR)) FROM pm_ProjectLink l WHERE l.Target = t.pm_ProjectId AND l.LinkType = 2) ".
 			   " ) SubProjects ";

        $columns[] =
            " CONCAT_WS(',',".
            "	(SELECT GROUP_CONCAT(CAST(l.Target AS CHAR)) FROM pm_ProjectLink l WHERE l.Source = t.pm_ProjectId AND l.LinkType = 2), ".
            "	(SELECT GROUP_CONCAT(CAST(l.Source AS CHAR)) FROM pm_ProjectLink l WHERE l.Target = t.pm_ProjectId AND l.LinkType = 1) ".
            " ) Programs ";

 		return $columns;
 	}
}

