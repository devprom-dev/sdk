<?php
define('HTML_IMPORT_ANCHOR', '/href="#([^"]+)"/i');

include_once SERVER_ROOT_PATH . "pm/views/wiki/parsers/WikiParser.php";
include_once "WikiImporterContentBuilder.php";
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

        $parts = preg_split('/\./', $documentTitle);
        $documentExt = array_pop($parts);
        $documentTitle = join('.', $parts);

        $this->filePath = $this->filePath.'.'.$documentExt;
        file_put_contents($this->filePath, $rawData);

        $html = $this->getHtml($this->filePath);
        if ( $html == '' ) return false;

        $html = preg_replace(REGEX_UID, '\\1<stop-parse>\\2</stop-parse>', $html);
        self::stripHeaders($html);

        $sections = preg_split('/<h[1-6][^>]*>/i', $html);
        $documentContent = array_shift($sections);

        if ( is_object($parent_it) && $parent_it->getId() != '' ) {
            $this->document_it = $parent_it;
        }
        else {
            $this->parseContent($documentContent);
            $this->document_it = $builder->buildDocument(
                $documentTitle, $documentContent, $this->options, $parent_it->getId()
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

            $title = preg_replace('/[\r\n]+/i', ' ', $title);
            $title = preg_replace('/^([\d]+\.)+/i', '', $title);
            $totext = new \Html2Text\Html2Text($title, array('width'=>0));
            $title = $totext->getText();
            $title = trim(str_replace('&nbsp;', ' ', $title));

            $this->parseContent($content);
            if ( $title == '' && $content == '' ) continue;

            $page_it = $builder->buildPage(
                $title, $content, $this->options, $levels[$selfLevel - 1]
            );
            if ( $this->document_it->getId() == '' ) $this->document_it = $page_it;

            $levels[$selfLevel] = $page_it->getId();
        }

        $builder->parsePages($this->document_it);

        return true;
    }

    function parseContent( &$content )
    {
        $content = trim(trim($content), PHP_EOL);

        // setup colspan for html tables (pandoc 1.19 doesn't support colspans)
        $tableParts = preg_split('/<table\s*/i', $content);
        foreach( $tableParts as $key => $tableContent ) {
            if ( $key == 0 ) continue;
            $tableColumnsSize = 0;
            $rowParts = preg_split('/<tr\s*/i', $tableContent);
            foreach( $rowParts as $rowKey => $rowContent ) {
                if ( $rowKey == 0 ) continue;
                $foundColumns = preg_split('/<(td|th)\s*/i', $rowContent);
                $foundColumnsSize = count($foundColumns);
                if ( $tableColumnsSize < 1 ) {
                    $tableColumnsSize = $foundColumnsSize;
                }
                elseif ( $foundColumnsSize < $tableColumnsSize ) {
                    $rowTail = array_pop($foundColumns);
                    $rowParts[$rowKey] = join('<td ', $foundColumns);
                    $rowParts[$rowKey] .= '<td colspan="'.($tableColumnsSize - $foundColumnsSize + 1).'" '.$rowTail;
                }
            }
            $tableParts[$key] = join('<tr ', $rowParts);
        }

        $content = join('<table ', $tableParts);
        $content = TextUtils::getValidHtml(TextUtils::getCleansedHtml($content));
        $content = preg_replace_callback('/<img([^>]+)>/', function($match) {
            return str_replace('style=', 'oldstyle=', $match[0]);
        }, $content);
        $content = preg_replace('/<hh([0-9])[^>]*>/', '<h\\1>', $content);
        $content = preg_replace('/<\/hh([0-9])>/', '</h\\1>', $content);
    }

    static function stripHeaders( &$html )
    {
        $tagsRestricted = array(
            'th', 'td', 'ul', 'ol'
        );
        foreach( $tagsRestricted as $tag ) {
            $lines = preg_split('/<'.$tag.'[^>]*>/i', $html);
            array_shift($lines);
            foreach( $lines as $line ) {
                $matches = array();
                if ( preg_match_all('/<h[1-6][^>]*>(.+)<\/h[1-6]>/i', array_shift(preg_split('/<\/'.$tag.'>/i', $line)), $matches, PREG_SET_ORDER) > 0 ) {
                    foreach( $matches as $match ) {
                        $html = str_replace($match[0], '<p>'.$match[1].'</p>', $html);
                    }
                }
            }
        }
    }

    function getDocumentIt() {
        return $this->document_it;
    }

    private $document_it = null;
    private $options = array();
    private $bookmarks = array();
}