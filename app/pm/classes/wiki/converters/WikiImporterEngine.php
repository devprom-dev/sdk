<?php
define('HTML_IMPORT_ANCHOR', '/href="#([^"]+)"/i');

include_once SERVER_ROOT_PATH . "pm/views/wiki/parsers/WikiParser.php";
include_once "WikiImporterContentBuilder.php";
include "WikiImporterListBuilder.php";

abstract class WikiImporterEngine
{
    const SyntaxTagsRemove = array('p','div','br','img','span');

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
        chmod($this->filePath, 0775);

        $html = $this->getHtml($this->filePath);
        if ( $html == '' ) return false;

        self::stripHeaders($html);

        $sections = preg_split('/<h[1-6][^>]*>/i', $html);

        $documentContent = array_shift($sections);
        $this->parseContent($documentContent);

        if ( is_object($parent_it) && $parent_it->getId() != '' ) {
            $this->document_it = $parent_it;
            $builder->storeDocumentContent($this->document_it->getId(), $documentContent);
        }
        else {
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
            $title = trim(\TextUtils::stripAnyTags(preg_replace('/[\r\n]+/i', ' ', $title)));

            $sectionNumber = '';
            if ( preg_match('/^(([\d]+\.?)+)/i', $title, $matches) ) {
                $sectionNumber = trim($matches[1], ' .');
            }
            $title = trim(preg_replace('/^(([\d]+\.?)+)/i', '', $title));

            $uid = '';
            if ( preg_match('/^\[([^\]]+)\]/i', $title, $matches) ) {
                $uid = trim($matches[1]);
            }
            $title = trim(preg_replace('/^(\[[^\]]+\])/i', '', $title));

            $this->parseContent($content);
            if ( $title == '' && $content == '' ) continue;

            $page_it = $builder->buildPage(
                $title, $content, $this->options, $levels[$selfLevel - 1], $this->document_it, $sectionNumber, $uid
            );

            $levels[$selfLevel] = $page_it->getId();
            \ZipSystem::sendResponse();
        }

        $builder->parsePages($this->document_it);

        return true;
    }

    function parseContent( &$content )
    {
        $content = trim(trim($content), PHP_EOL);

        $content =
            $this->parseUMLSyntax(
                $this->parseMathSyntax(
                    $this->parseCodeSyntax($content)
                )
            );

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

        $content = \TextUtils::getValidHtml(\TextUtils::getCleansedHtml($content));
        $content = preg_replace('/<hh([0-9])[^>]*>/', '<h\\1>', $content);
        $content = preg_replace('/<\/hh([0-9])>/', '</h\\1>', $content);
    }

    function parseUMLSyntax( $content )
    {
        $parts = explode('@startuml', $content);
        $content = array_shift($parts);
        foreach( $parts as $part ) {
            list($umlContent, $text) = explode('@enduml', $part);
            $umlContent = $this->convertSyntax($umlContent);
            $umlContent = html_entity_decode($umlContent);
            $umlContent = '<img uml="'.base64_encode(rawurlencode($umlContent)).'" src="'.\TextUtils::getPlantUMLUrl($umlContent).'">';
            $content .= $umlContent . $text;
        }
        return $content;
    }

    function parseMathSyntax( $content )
    {
        $parts = explode('@startmath', $content);
        $content = array_shift($parts);
        foreach( $parts as $part ) {
            list($mathFormula, $text) = explode('@endmath', $part);
            $mathFormula = $this->convertSyntax($mathFormula);
            $mathFormula = html_entity_decode($mathFormula);
            $mathJson = array(
                'math' => $mathFormula,
                'classes' => array(
                    'math-tex' => 1
                )
            );
            $mathFormula = '<span class="math-tex cke_widget_element" data-widget="mathjax" data-cke-widget-data="'.rawurlencode(\JsonWrapper::encode($mathJson)).'"><iframe></iframe></span>';
            $content .= $mathFormula . $text;
        }
        return $content;
    }

    function parseCodeSyntax( $content )
    {
        $parts = explode('@startcode', $content);
        $content = array_shift($parts);
        foreach( $parts as $part ) {
            list($code, $text) = explode('@endcode', $part);
            $language = 'xml';
            if ( preg_match('/,language-([^\s]+)/i', $code, $matches) ) {
                $language = $matches[1];
                $code = preg_replace('/,language-([^\s]+)/i', '', $code);
            }
            $code = $this->convertSyntax($code);
            $code = '<pre><code class="language-'.$language.' hljs">'.html_entity_decode($code).'</code></pre>';
            $content .= $code . $text;
        }
        return $content;
    }

    function convertSyntax( $content ) {
        $content = preg_replace('/(<\/p>|<\/div>|<\/?br\/?>)/i', '\\1'.PHP_EOL, $content);
        foreach( self::SyntaxTagsRemove as $htmlTag ) {
            $content = \TextUtils::skipHtmlTag($htmlTag, $content);
        }
        return $content;
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

    function getOptions() {
        return $this->options;
    }

    private $document_it = null;
    private $options = array();
    private $bookmarks = array();
}