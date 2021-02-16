<?php

class DurationPersister extends ObjectSQLPersister
{
    function getSelectColumns( $alias )
    {
        $columns = array();
        $alias = $alias != '' ? $alias."." : "";

        $object = $this->getObject();
        $objectPK = $alias.$object->getClassName().'Id';

        array_push( $columns,
            " (SELECT UNIX_TIMESTAMP(ru.RecordModified) - UNIX_TIMESTAMP(ru.RecordCreated)" .
            "    FROM co_JobRun ru " .
            "   WHERE ru.ScheduledJob = ".$objectPK.
            "   ORDER BY ru.RecordModified DESC LIMIT 1) LastDuration " );

        array_push( $columns,
            " (SELECT AVG(UNIX_TIMESTAMP(ru.RecordModified) - UNIX_TIMESTAMP(ru.RecordCreated)) " .
            "    FROM co_JobRun ru " .
            "   WHERE ru.ScheduledJob = ".$objectPK.") AverageDuration " );

        return $columns;
    }
}
