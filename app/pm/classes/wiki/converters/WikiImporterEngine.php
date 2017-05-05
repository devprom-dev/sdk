<?php
define('HTML_IMPORT_ANCHOR', '/href="#([^"]+)"/i');

include_once SERVER_ROOT_PATH . "pm/views/wiki/parsers/WikiParser.php";
include "WikiImporterContentBuilder.php";
include "WikiImporterListBuilder.php";

abstract class WikiImporterEngine
{
    abstract protected function getHtml( $filePath );

    function __construct()
    {
        $this->filePath = str_replace("\\", "/", realpath(tempnam(SERVER_FILES_PATH, "pandocoutput")));
    }

    function __destruct() {
        unlink($this->filePath);
    }

    public function setOptions( $data ) {
        $this->options = $data;
    }

    public function import( $builder, $documentTitle, $rawData, $parent_it )
    {
        \FeatureTouch::Instance()->touch('import-wikipage-raw');

        $info = pathinfo($documentTitle);
        $documentTitle = $info['filename'];

        $this->filePath = $this->filePath.'.'.$info['extension'];
        file_put_contents($this->filePath, $rawData);

        $html = $this->getHtml($this->filePath);
        if ( $html == '' ) return false;

        $html = preg_replace(REGEX_UID, '\\1<stop-parse>\\2</stop-parse>', $html);

        $sections = preg_split('/<h[1-6][^>]*>/i', $html);
        $documentContent = array_shift($sections);

        if ( is_object($parent_it) && $parent_it->getId() != '' ) {
            $this->document_it = $parent_it;
        }
        else {
            $this->document_it = $builder->buildDocument(
                $documentTitle, $documentContent, $parent_it->getId()
            );
        }

        $levels = array ();
        foreach( range(0, 6) as $level ) {
            $levels[$level] = $this->document_it->getId();
        }

        foreach( $sections as $section ) {
            preg_match('/<\/h([1-6])>/i', $section, $matches);
            $selfLevel = $matches[1];

            list($title, $content) = preg_split('/<\/h[1-6]>/i', $section);

            $totext = new \Html2Text\Html2Text(trim($title), array('width'=>0));
            $title = preg_replace('/[\r\n]+/', ' ', $totext->getText());

            $content = trim(trim($content), PHP_EOL);
            if ( $title == '' && $content == '' ) continue;

            $page_it = $builder->buildPage(
                $title, $content, $this->options, $levels[$selfLevel - 1]
            );
            if ( $this->document_it->getId() == '' ) $this->document_it = $page_it;

            $levels[$selfLevel] = $page_it->getId();
        }

        $pageIt = $this->document_it->object->getRegistry()->Query(
            array(
                new FilterAttributePredicate('DocumentId', $this->document_it->getId()),
                new SortDocumentClause()
            )
        );
        while( !$pageIt->end() ) {
            $this->parsePage($pageIt);
            $pageIt->moveNext();
        }

        return true;
    }

    protected function parsePage( $pageIt )
    {
        $content = $pageIt->getHtmlDecoded('Content');
        $result = preg_replace_callback(HTML_IMPORT_ANCHOR, array($this, 'parsePageAnchors'), $content);

        if ( $result != $content ) {
            $pageIt->object->getRegistry()->Store( $pageIt,
                array(
                    'Content' => $result
                )
            );
        }
    }

    function parsePageAnchors( $match )
    {
        $refIt = $this->document_it->object->getRegistry()->Query(
            array(
                new FilterAttributePredicate('DocumentId', $this->document_it->getId()),
                new FilterSearchAttributesPredicate(urldecode(preg_replace('/[_-]/', ' ', $match[1])), array('Caption'))
            )
        );
        if ( $refIt->getId() == '' ) return $match[0];

        $uid = new ObjectUID();
        $info = $uid->getUIDInfo($refIt);
        return 'href="'.$info['url'].'"';
    }

    function getDocumentIt() {
        return $this->document_it;
    }

    private $document_it = null;
    private $options = array();
    private $bookmarks = array();
}