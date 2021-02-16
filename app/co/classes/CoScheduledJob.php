<?php
include "CoScheduledJobIterator.php";
include "persisters/DurationPersister.php";

class CoScheduledJob extends Metaobject
{
    function CoScheduledJob()
    {
        parent::Metaobject('co_ScheduledJob');
        $this->addPersister( new DurationPersister() );
        $this->setSortDefault( new SortOrderedClause() );
    }

    function createIterator()
    {
        return new CoScheduledJobIterator( $this );
    }
}
