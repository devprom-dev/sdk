<?php

class WorkflowTimeScaleRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator( array (
                array ( 'entityId' => 1, 'ReferenceName' => 'hours', 'Caption' => translate('часы') ),
                array ( 'entityId' => 2, 'ReferenceName' => 'days', 'Caption' => translate('дни') )
        ));
    }
}