<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterBuilder.php";
include "converters/WikiImporterEnginePanDoc.php";
include "converters/WikiImporterEngineExcel.php";
include "converters/WikiImporterEngineHtml.php";
include "converters/WikiImporterEngineMhtml.php";
include "converters/WikiImporterEnginePdf.php";
include "converters/WikiImporterEngineConfluence.php";

class WikiImporterBuilderPanDoc extends WikiImporterBuilder
{
    private $pandocEnabled = null;

    function __sleep()
    {
        $this->checkPandocInstalled();
        return array (
            'pandocEnabled'
        );
    }

    public function build( WikiImporterRegistry $registry, Metaobject $page )
    {
        $registry->add('WikiImporterEngineConfluence');
        $registry->add('WikiImporterEngineMhtml');
        $registry->add('WikiImporterEngineExcel');
        $registry->add('WikiImporterEnginePdf');
        $registry->add('WikiImporterEngineHtml');
        if ( !$this->checkPandocInstalled() ) return;
        $registry->add('WikiImporterEnginePanDoc');
    }

    protected function checkPandocInstalled() {
        if ( !is_null($this->pandocEnabled) ) return $this->pandocEnabled;
        return $this->pandocEnabled = strpos(shell_exec("pandoc -v 2>&1"), 'pandoc') !== false;
    }
}