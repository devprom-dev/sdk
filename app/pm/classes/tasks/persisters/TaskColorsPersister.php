<?php

class TaskColorsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		return array(
            " (SELECT tp.RelatedColor FROM pm_TaskType tp WHERE tp.pm_TaskTypeId = t.TaskType) TypeColor ",
            " (SELECT tp.RelatedColor FROM Priority tp WHERE tp.PriorityId = t.Priority) PriorityColor "
        );
 	}

 	function IsPersisterImportant() {
        return true;
    }
}
