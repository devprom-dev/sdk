<?php

class IssueAuthorActiveRegistry extends IssueAuthorRegistry
{
    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array(
                new FilterAttributeLesserPredicate('Blocks', 1)
            )
        );
    }
}