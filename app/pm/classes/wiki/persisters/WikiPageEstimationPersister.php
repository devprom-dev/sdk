<?php

class WikiPageEstimationPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('Estimation');
    }

    function getSelectColumns( $alias )
 	{
        $columns = array(
            "  IFNULL(
                 (SELECT SUM(
                            IF(c.Includes IS NULL, c.Estimation, 
                                (SELECT i.Estimation FROM WikiPage i WHERE i.WikiPageId = c.Includes))) 
                   FROM WikiPage c 
                  WHERE c.DocumentId = t.DocumentId 
                    AND c.ParentPath LIKE CONCAT({$alias}.ParentPath, '%')) ,0) EstimationHie "
        );

        return $columns;
 	}
}
