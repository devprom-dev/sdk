<?php
include_once "WikiConverterBuilder.php";

class WikiConverterBuilderCommon extends WikiConverterBuilder
{
    function build(WikiConverterRegistry $registry, Metaobject $page)
    {
        $registry->add('WikiIteratorExportPdf', 'PDF');
        $registry->add('WikiIteratorExportHtml', 'HTML');
    }
}