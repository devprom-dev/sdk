<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/converters/WikiConverterBuilder.php";
include "converters/WikiConverterPanDocMSWord.php";
include "converters/WikiConverterPanDocODF.php";

class WikiConverterBuilderWYSIWYG extends WikiConverterBuilder
{
    private $pandocEnabled = null;

    function __sleep()
    {
        $this->checkPandocInstalled();
        return array (
            'pandocEnabled'
        );
    }

    function build(WikiConverterRegistry $registry, Metaobject $page)
    {
        if ( $this->checkPandocInstalled() ) {
            $registry->add('WikiConverterPanDocODF', text('wrtfckeditor6'));
            $registry->add('WikiConverterPanDocMSWord', text('wrtfckeditor5'));
        }
    }

    protected function checkPandocInstalled() {
        if ( !is_null($this->pandocEnabled) ) return $this->pandocEnabled;
        return $this->pandocEnabled = strpos(shell_exec("pandoc -v 2>&1"), 'pandoc') !== false;
    }
}