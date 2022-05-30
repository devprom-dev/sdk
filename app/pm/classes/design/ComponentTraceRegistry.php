<?php

class ComponentTraceRegistry extends ObjectRegistrySQL
{
 	function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array(
                new FilterAttributePredicate('ObjectClass', $this->getObject()->getObjectClass())
            )
        );
    }
}