<?php
include_once "WikiConverterPanDoc.php";

class WikiConverterPanDocMSWord extends WikiConverterPanDoc
{
    function getToParms()
    {
        return "--to=docx";
    }

    function getExtension()
    {
        return ".docx";
    }

    function getMime()
    {
        return "application/msword";
    }

    function getTemplateParms( $filePath )
    {
        return '--reference-docx="'.$filePath.'"';
    }
}
