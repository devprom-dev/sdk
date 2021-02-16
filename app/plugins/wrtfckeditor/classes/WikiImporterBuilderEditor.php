<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterBuilder.php";
include "converters/WikiImporterEnginePanDoc.php";
include "converters/WikiImporterEngineExcel.php";
include "converters/WikiImporterEngineHtml.php";
include "converters/WikiImporterEngineMhtml.php";
include "converters/WikiImporterEnginePdf.php";
include "converters/WikiImporterEngineConfluence.php";
include "converters/WikiImporterEngineLibreOffice.php";

class WikiImporterBuilderEditor extends WikiImporterBuilder
{
    private $pandocEnabled = null;
    private $libreOfficeEnabled = null;

    function __sleep()
    {
        $this->checkPandocInstalled();
        return array (
            'pandocEnabled',
            'libreOfficeEnabled'
        );
    }

    public function build( WikiImporterRegistry $registry, Metaobject $page )
    {
        $registry->add('WikiImporterEngineConfluence');
        $registry->add('WikiImporterEngineMhtml');
        $registry->add('WikiImporterEngineExcel');
        $registry->add('WikiImporterEnginePdf');
        $registry->add('WikiImporterEngineHtml');
        if ( $this->checkLibreOfficeInstalled() ) {
            $registry->add('WikiImporterEngineLibreOffice');
        }
        if ( $this->checkPandocInstalled() ) {
            $registry->add('WikiImporterEnginePanDoc');
        }
    }

    protected function checkPandocInstalled() {
        try {
            if ( !is_null($this->pandocEnabled) ) return $this->pandocEnabled;
            return $this->pandocEnabled = stripos(\FileSystem::execPanDoc(), 'pandoc') !== false;
        } catch( \Exception $e ) {
            \Logger::getLogger('System')->error($e->getMessage());
            return false;
        }
    }

    protected function checkLibreOfficeInstalled() {
        try {
            if ( !is_null($this->libreOfficeEnabled) ) return $this->libreOfficeEnabled;
            return $this->libreOfficeEnabled = stripos(\FileSystem::execLibreOffice(), 'office') !== false;
        } catch( \Exception $e ) {
            \Logger::getLogger('System')->error($e->getMessage());
            return false;
        }
    }
}