<?php

class RequestColorsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
            " (SELECT tp.RelatedColor FROM pm_IssueType tp WHERE tp.pm_IssueTypeId = t.Type) TypeColor ",
            " (SELECT tp.RelatedColor FROM Priority tp WHERE tp.PriorityId = t.Priority) PriorityColor "
        );
 	}

 	function IsPersisterImportant() {
        return true;
    }
}
