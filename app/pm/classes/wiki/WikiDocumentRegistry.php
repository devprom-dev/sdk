<?php
include_once "persisters/DocumentStatePersister.php";
include_once "persisters/DocumentVersionPersister.php";

class WikiDocumentRegistry extends ObjectRegistrySQL
{
	function getPersisters()
    {
        return array(
            new DocumentVersionPersister(),
            new DocumentStatePersister()
        );
    }

    function getFilters()
    {
        return array_merge(
            parent::getFilters(),
            array(
                new WikiRootFilter()
            )
        );
    }
}