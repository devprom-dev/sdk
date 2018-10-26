<?php
include "WikiDocumentRegistry.php";
include "WikiDocumentIterator.php";

class WikiDocument extends PMWikiPage
{
    function __construct() {
        parent::__construct(new WikiDocumentRegistry($this));
    }

    function createIterator() {
        return new WikiDocumentIterator($this);
    }
}