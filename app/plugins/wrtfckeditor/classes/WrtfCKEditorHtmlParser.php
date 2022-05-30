<?php
use Devprom\ProjectBundle\Service\Model\ModelService;

define('REGEX_IMAGE_NUMBERING', '/<figcaption[^>]*>(.*)<\/figcaption>/i');
define('REGEX_TABLE_NUMBERING', '/<table([^>]*)>\s*<caption([^>]*)>(.+)<\/caption>/i');

class WrtfCKEditorHtmlParser extends WrtfCKEditorPageParser
{
    private static $imageNumber = 1;
    private static $tableNumber = 1;

 	function parse( $content = null )
	{
	    $content = parent::parse($content);
	    $content = preg_replace('/<p>(\xA0|\s)*<\/p>/i', '', $content);
        $content = preg_replace('/<figure/i', '<center><figure', $content);
        $content = preg_replace('/<\/figure>/i', '</figure></center>', $content);
        $this->resetCodeBlocks();

        $callbacks = array (
            CODE_ISOLATE => array($this, 'codeIsolate'),
            '/<img\s([^>]+)>/i' => array($this, 'parseUMLImage'),
            IMAGE_ISOLATE => array($this, 'imageIsolate'),
            REGEX_INCLUDE_REVISION => array($this, 'parseIncludeRevisionCallback'),
            REGEX_INCLUDE_PAGE => array($this, 'parseIncludePageCallback'),
            REGEX_MATH_TEX => array($this, 'parseMathTex'),
            REGEX_FIELD_SUBSTITUTION => array($this, 'parseFieldSubstitution'),
            REGEX_IMAGE_NUMBERING => array($this, 'imageNumbering'),
            REGEX_TABLE_NUMBERING => array($this, 'tableNumbering'),
            IMAGE_RESTORE => array($this, 'imageRestore'),
            CODE_RESTORE => array($this, 'codeRestore')
        );
        if ( function_exists('preg_replace_callback_array') ) {
            return preg_replace_callback_array($callbacks, $content);
        }
        else {
            foreach( $callbacks as $regexp => $callback ) {
                $content = preg_replace_callback($regexp, $callback, $content);
            }
            return $content;
        }
	}

	function imageNumbering( $match ) {
        return '<figcaption>'.
            trim(preg_replace('/%1/', self::$imageNumber++,
                preg_replace('/%2/', $match[1], text('doc.images.numbering'))), '.').
                    '</figcaption>';
    }

    function tableNumbering( $match ) {
        return '<table '.$match[1].'><caption '.$match[2].'>'.
            trim(preg_replace('/%1/', self::$tableNumber++,
                preg_replace('/%2/', $match[3], text('doc.tables.numbering'))),'.').
                    '</caption>';
    }

    function parseIncludeRevisionCallback( $match )
    {
        $info = $this->getUidInfo(trim($match[1], '[]'));
        $object_it = $info['object_it'];
        $content = '';

        if ( !is_object($object_it) || $object_it->getId() < 1 ) {
            return str_replace('%1', $match[1], text(1166));
        }

        $revisions = preg_split('/-/', $match[2]);

        $pageIt = (new WikiPageRegistryContent($object_it->object))->Query(
            array(
                new ParentTransitiveFilter($object_it->getId()),
                new FilterAttributePredicate('DocumentId', $object_it->get('DocumentId')),
                new SortDocumentClause()
            )
        );
        $removeSectionNumberHead = $pageIt->get('SectionNumber');
        if ( $removeSectionNumberHead != '' ) $removeSectionNumberHead .= '.';

        $freshContent = '';
        while ( !$pageIt->end() ) {
            $freshContent .= $this->buildDocumentStructure($removeSectionNumberHead,
                $pageIt, 'Content');
            $pageIt->moveNext();
        }

        $pageChangedIt = (new WikiPageRegistryContent($object_it->object))->Query(
            array(
                new ParentTransitiveFilter($object_it->getId()),
                new WikiPageAfterRevisionPersister($revisions[0]),
                new FilterAttributePredicate('DocumentId', $object_it->get('DocumentId')),
                new SortDocumentClause()
            )
        );
        $wasContent = '';
        while ( !$pageChangedIt->end() ) {
            $wasContent .= $this->buildDocumentStructure($removeSectionNumberHead,
                $pageChangedIt, 'Content');
            $pageChangedIt->moveNext();
        }

        $parser = new WrtfCKEditorComparerParser($this->getObjectIt());
        $wasContent = $parser->parse($wasContent);
        $freshContent = $parser->parse($freshContent);

        $content .= '<div class="reset wysiwyg">';
        $diffBuilder = new WikiHtmlDiff($wasContent, $freshContent);
        $diffContent = $diffBuilder->build();
        if ( $diffContent == '' ) $diffContent = $freshContent;
        $content .= $diffContent . '</div>';

        $content = '<div class="wiki-page-help">'.
            sprintf(text(2332), '<a target="_blank" href="'.$info['url'].'">'.$info['uid'].'</a>').'</div>' . $content;
        return '<div class="inline-page">' . $content . '</div>';
    }

    function parseUMLImage( $match ) {
        return $match[0];
    }

    function parseFieldSubstitution( $match )
    {
        $result = ModelService::computeFormula(
            $this->getObjectIt(),
            '{' . $match[1] . '}'
        );
        $lines = array();
        foreach ($result as $computedItem) {
            if (!is_object($computedItem)) {
                $lines[] = TextUtils::stripAnyTags($computedItem);
            } else {
                $lines[] = $computedItem->getDisplayName();
            }
        }
        return join(', ', $lines);
    }
}
