<?php

class RequestAttachmentRegistry extends ObjectRegistrySQL
{
    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array (
                new FilterAttributePredicate('ObjectClass', array('request','issue','increment'))
            )
        );
    }
}