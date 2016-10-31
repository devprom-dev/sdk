<?php
include_once "WikiConverterPanDoc.php";

class WikiConverterPanDocODF extends WikiConverterPanDoc
{
    function getToParms()
    {
        return "--to=odt";
    }

    function getExtension()
    {
        return ".odt";
    }

    function getMime()
    {
        return "application/vnd.oasis.opendocument.text";
    }

    function getTemplateParms( $filePath )
    {
        return '--reference-odt="'.$filePath.'"';
    }
}
