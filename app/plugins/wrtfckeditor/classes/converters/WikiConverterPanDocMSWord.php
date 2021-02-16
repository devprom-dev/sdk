<?php
include_once "WikiConverterPanDoc.php";
include_once "MSWordTemplateService.php";

class WikiConverterPanDocMSWord extends WikiConverterPanDoc
{
    use MSWordTemplateService;

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
        if (version_compare($this->getVersion(), '2.0.0') >= 0) {
            return '--reference-doc="'.$filePath.'"';
        }
        else {
            return '--reference-docx="'.$filePath.'"';
        }
    }

    protected function getDefaultTemplatePath()
    {
        if (version_compare($this->getVersion(), '2.0.0') >= 0) {
            return SERVER_ROOT_PATH."templates/config/pandoc/reference.docx";
        }
        else {
            return SERVER_ROOT_PATH."templates/config/pandoc/reference1.docx";
        }
    }
}
