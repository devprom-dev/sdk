<?php

class WikiDocumentRegistry extends ObjectRegistrySQL
{
	function getPersisters() {
        return array(
            new DocumentVersionPersister()
        );
    }

    function getFilters() {
        return array_merge(
            parent::getFilters(),
            array(
                new WikiRootFilter()
            )
        );
    }
}