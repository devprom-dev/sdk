<?php

class RequestCommentRegistry extends ObjectRegistrySQL
{
    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array (
                new FilterAttributePredicate('ObjectClass', array('Request','Issue','Increment'))
            )
        );
    }
}