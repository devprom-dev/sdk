<?php

class CustomAttributeValueVpdPredicate extends FilterPredicate
{
    function __construct() {
        parent::__construct('dummy');
    }

    function _predicate( $filter )
 	{
        $vpdValue = getFactory()->getObject('pm_CustomAttribute')->getVpdValue();
        if ( $vpdValue == '' ) return ' AND 1 = 2 ';

        return " AND EXISTS (SELECT 1 FROM pm_CustomAttribute a
                              WHERE a.pm_CustomAttributeId = t.CustomAttribute
                                AND a.VPD = '{$vpdValue}') ";
 	}
}
