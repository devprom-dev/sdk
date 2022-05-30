<?php

class ReleaseRecentRegistry extends ReleaseRegistry
{
    function getFilters() {
        return array_merge (
            parent::getFilters(),
            array (
                new FilterAttributePredicate('IsClosed', 'N')
            )
        );
    }
}