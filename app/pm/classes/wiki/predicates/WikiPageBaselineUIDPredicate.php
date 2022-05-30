<?php

class WikiPageBaselineUIDPredicate extends FilterPredicate
{
    function _predicate( $filter )
    {
        return " AND EXISTS (
                    SELECT 1 FROM WikiPage p
                     WHERE p.WikiPageId = t.ObjectId AND p.UID = '{$filter}')
                 AND EXISTS (
                    SELECT 1 FROM pm_Project pr
                     WHERE pr.VPD = t.VPD AND pr.IsClosed = 'N')";
    }
}