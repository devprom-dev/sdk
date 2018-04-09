<?php

class VersionRegistry extends ObjectRegistrySQL
{
 	function getQueryClause()
 	{
        if ( class_exists(getFactory()->getClass('Build')) ) {
            $sql =
                " SELECT concat(LPAD('',8,'0'), '.', LPAD(b.Caption,8,'0')) VersionNumber, " .
                "		CONCAT(IFNULL(CONCAT(b.Application,': '), ''),b.Caption) Caption, " .
                "		CONCAT(IFNULL(CONCAT(b.Application,': '), ''),b.Caption) pm_VersionId, " .
                "		'' Version, " .
                "		'' `Release`, ".
                "       b.pm_BuildId Build, " .
                "		b.Description, ".
                "		(SELECT v.pm_ProjectId FROM pm_Project v WHERE v.VPD = b.VPD) Project, ".
                "        b.VPD, ".
                "        NOW() StartDate, NOW() FinishDate, b.RecordCreated, b.RecordModified " .
                "   FROM pm_Build b ".
                "  WHERE 1 = 1 ".getFactory()->getObject('Build')->getVpdPredicate('b');
        }
        else {
            $sql = " SELECT 1 ";
        }

		return "(".$sql.")";
 	}	
}