<?php

class FeatureProgressPersister extends ObjectSQLPersister
{
    function getAttributes()
    {
        return array(
            'Progress'
        );
    }

    function getSelectColumns( $alias )
 	{
 		return array( 
 			" ( SELECT COUNT(DISTINCT l.pm_ChangeRequestId) " .
			"     FROM pm_ChangeRequest l, pm_Function f " .
			"    WHERE l.Function = f.pm_FunctionId " .
            "      AND f.ParentPath LIKE CONCAT('%,',".$this->getPK($alias).",',%') ) TotalRequirements ",

            " ( SELECT COUNT(DISTINCT l.pm_ChangeRequestId) " .
            "     FROM pm_ChangeRequest l, pm_Function f " .
            "    WHERE l.Function = f.pm_FunctionId " .
            "      AND f.ParentPath LIKE CONCAT('%,',".$this->getPK($alias).",',%') ".
            "      AND l.FinishDate IS NOT NULL) CompletedRequirements ",
 		);
 	}
}
