<?php
include "predicates/KnowledgeBaseAccessPredicate.php";

class ProjectPageRegistry extends WikiPageRegistry
{
    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array(
                new KnowledgeBaseAccessPredicate()
            )
        );
    }
}