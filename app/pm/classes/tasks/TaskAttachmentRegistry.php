<?php

class TaskAttachmentRegistry extends ObjectRegistrySQL
{
    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array (
                new FilterAttributePredicate('ObjectClass', 'task')
            )
        );
    }
}