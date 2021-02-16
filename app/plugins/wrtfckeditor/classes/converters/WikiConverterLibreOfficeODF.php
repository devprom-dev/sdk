<?php
include_once "WikiConverterLibreOffice.php";

class WikiConverterLibreOfficeODF extends WikiConverterLibreOffice
{
    protected function getToParms() {
        return 'odt:"writer8"';
    }

    protected function getExtension() {
        return ".odt";
    }

    protected function getMime() {
        return "application/vnd.oasis.opendocument.text";
    }
}
