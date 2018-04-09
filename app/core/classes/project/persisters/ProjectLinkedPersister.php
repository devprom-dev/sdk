<?php

class ProjectLinkedPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    $columns = array();
 	    
		$sql = " CONCAT_WS(',', ".
		       "    (SELECT GROUP_CONCAT(CAST(pr.pm_ProjectId AS CHAR)) " .
			   "       FROM pm_Project pr, pm_ProjectLink pl " .
			   "      WHERE pl.Target = pr.pm_ProjectId" .
			   "        AND pl.Source = ".$this->getPK($alias).
			   "		AND pl.LinkType = 1 ".
			   "        AND IFNULL(pr.IsClosed, 'N') = 'N'), ".
			   "    (SELECT GROUP_CONCAT(CAST(pr.pm_ProjectId AS CHAR)) " .
			   "       FROM pm_Project pr, pm_ProjectLink pl " .
			   "      WHERE pl.Source = pr.pm_ProjectId" .
			   "        AND pl.Target = ".$this->getPK($alias).
			   "		AND pl.LinkType = 2 ".
			   "        AND IFNULL(pr.IsClosed, 'N') = 'N'), ".
			   "    (SELECT GROUP_CONCAT(CAST(pr.pm_ProjectId AS CHAR)) " .
			   "       FROM pm_Project pr, pm_Project pr2, pm_ProjectLink pl " .
			   "      WHERE pl.Target = pr.pm_ProjectId" .
			   "        AND pl.Source = ".$this->getPK($alias).
			   "		AND pl.Source = pr2.pm_ProjectId ".
			   "		AND pr2.IsTender = 'N' ".
			   "		AND pl.LinkType = 2 ), ".
			   "    (SELECT GROUP_CONCAT(CAST(pr.pm_ProjectId AS CHAR)) " .
			   "       FROM pm_Project pr, pm_Project pr2, pm_ProjectLink pl " .
			   "      WHERE pl.Source = pr.pm_ProjectId" .
			   "        AND pl.Target = ".$this->getPK($alias).
			   "		AND pl.Target = pr2.pm_ProjectId ".
			   "		AND pr2.IsTender = 'N' ".
			   "		AND pl.LinkType = 1 ) ".
			   "  ) LinkedProject ";
 	    
 		$columns[] = $sql;

 		$columns[] =
            "    (SELECT GROUP_CONCAT(CAST(pr.pm_ProjectId AS CHAR)) " .
            "       FROM co_ProjectGroupLink pg1, co_ProjectGroupLink pg2, pm_Project pr " .
            "      WHERE pg1.ProjectGroup = pg2.ProjectGroup" .
            "        AND pg1.Project = ".$this->getPK($alias).
            "        AND pg2.Project = pr.pm_ProjectId".
            "        AND IFNULL(pr.IsClosed, 'N') = 'N') PortfolioProject ";
 	    
 		return $columns;
 	}
}