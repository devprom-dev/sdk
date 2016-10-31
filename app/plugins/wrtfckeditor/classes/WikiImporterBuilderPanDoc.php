<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiImporterBuilder.php";
include "converters/WikiImporterEnginePanDoc.php";

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

    protected function checkPandocInstalled() {
        if ( !is_null($this->pandocEnabled) ) return $this->pandocEnabled;
        return $this->pandocEnabled = strpos(shell_exec("pandoc -v 2>&1"), 'pandoc') !== false;
    }

    public function build( WikiImporterRegistry $registry, Metaobject $page )
    {
        if ( !$this->checkPandocInstalled() ) return;
        $registry->add('WikiImporterEnginePanDoc');
    }
}