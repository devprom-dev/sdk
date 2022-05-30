<?php
include "predicates/KnowledgeBaseAccessPredicate.php";

class ProjectPageRegistry extends WikiPageRegistryContent
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