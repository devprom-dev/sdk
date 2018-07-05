<?php
include "WikiDocumentRegistry.php";

class WikiDocument extends PMWikiPage
{
    function __construct()
    {
        parent::__construct(new WikiDocumentRegistry($this));
    }
}