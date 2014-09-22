<?php

class ProjectStatePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		 switch( $filter )
		 {
		 	case 'current':
		 		return " AND IFNULL(t.IsClosed, 'N') = 'N' " .
		 			   " AND EXISTS ( SELECT 1 FROM pm_Version v " .
		 			   "			   WHERE v.Project = t.pm_ProjectId" .
		 			   "				 AND TO_DAYS(NOW()) BETWEEN " .
		 			   "					(SELECT m.MetricValue FROM pm_VersionMetric m" .
		 			   "	   				  WHERE v.pm_VersionId = m.Version" .
		 			   "		 			    AND m.Metric = 'EstimatedStart') AND ".
		 			   "					(SELECT m.MetricValue FROM pm_VersionMetric m" .
		 			   "	   				  WHERE v.pm_VersionId = m.Version" .
		 			   "		 			    AND m.Metric = 'EstimatedFinish')" .
		 			   "			) ";

		 	case 'active':
		 		return " AND IFNULL(t.IsClosed, 'N') = 'N' ";

		 	case 'closed':
		 		return " AND IFNULL(t.IsClosed, 'N') = 'Y' ";
		 }
 	}
}