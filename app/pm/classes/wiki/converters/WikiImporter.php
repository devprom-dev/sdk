<?php
include "WikiImporterRegistry.php";

class WikiImporter extends Metaobject
{
    function __construct( Metaobject $page )
    {
        $registry = new WikiImporterRegistry($this);
        $registry->setWikiPage($page);
        parent::__construct('entity', $registry);
    }
}