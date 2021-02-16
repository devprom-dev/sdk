<?php
include_once "WikiConverterLibreOffice.php";
include_once "MSWordTemplateService.php";

class WikiConverterLibreOfficeMSWord extends WikiConverterLibreOffice
{
    use MSWordTemplateService;

    protected function getToParms() {
        return 'docx:"MS Word 2007 XML"';
    }

    protected function getExtension() {
        return ".docx";
    }

    protected function getMime() {
        return "application/msword";
    }
}
