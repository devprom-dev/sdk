<?php
include "WikiConverterRegistry.php";

class WikiConverter extends Metaobject
{
    function __construct( Metaobject $page )
    {
        $registry = new WikiConverterRegistry($this);
        $registry->setWikiPage($page);
        parent::__construct('entity', $registry);
    }
}