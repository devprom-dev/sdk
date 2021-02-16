<?php

class RequestSelectivePredicate extends FilterPredicate
{
    function __construct() {
        parent::__construct('dummy');
    }

    function _predicate( $filter )
 	{
        return " AND t.VPD IN (SELECT m.VPD FROM pm_Methodology m 
                                WHERE m.IsRequirements = '".ReqManagementModeRegistry::RDD."'
                                  AND t.Type IS NOT NULL
                                UNION
                               SELECT m.VPD FROM pm_Methodology m 
                                WHERE m.IsRequirements <> '".ReqManagementModeRegistry::RDD."')";
 	}
} 
