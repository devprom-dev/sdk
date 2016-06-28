<?php

class TaskCommentRegistry extends ObjectRegistrySQL
{
    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array (
                new FilterAttributePredicate('ObjectClass', 'Task')
            )
        );
    }
}