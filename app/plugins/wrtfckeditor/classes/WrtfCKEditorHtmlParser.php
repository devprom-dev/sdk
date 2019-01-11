<?php

define('REGEX_IMAGE_NUMBERING', '/<figcaption[^>]*>(.*)<\/figcaption>/i');
define('REGEX_TABLE_NUMBERING', '/<table([^>]*)>\s*<caption([^>]*)>(.+)<\/caption>/i');

class WrtfCKEditorHtmlParser extends WrtfCKEditorPageParser
{
    private static $imageNumber = 1;
    private static $tableNumber = 1;

 	function parse( $content = null )
	{
	    $content = parent::parse($content);
	    $content = preg_replace('/<p>(\xA0|\s|\&nbsp;)*<\/p>/i', '', $content);
        $content = preg_replace('/<figure/i', '<center><figure', $content);
        $content = preg_replace('/<\/figure>/i', '</figure></center>', $content);
        $this->resetCodeBlocks();

        $callbacks = array (
            CODE_ISOLATE => array($this, 'codeIsolate'),
            REGEX_INCLUDE_REVISION => array($this, 'parseIncludeRevisionCallback'),
            REGEX_INCLUDE_PAGE => array($this, 'parseIncludePageCallback'),
            REGEX_MATH_TEX => array($this, 'parseMathTex'),
            REGEX_IMAGE_NUMBERING => array($this, 'imageNumbering'),
            REGEX_TABLE_NUMBERING => array($this, 'tableNumbering'),
            TextUtils::REGEX_SHRINK => array(TextUtils::class, 'shrinkLongUrl'),
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
        $changeIt = getFactory()->getObject('WikiPageChange')->getExact($revisions[0]);

        if ( $changeIt->getId() != '' ) {
            if ( count($revisions) > 1 ) {
                if ( $revisions[1] > 0 ) {
                    $freshContent = $changeIt->object->getExact($revisions[1])->getHtmlDecoded('Content');
                }
                else {
                    $freshContent = $object_it->getHtmlDecoded('Content');
                }
                $parser = new WrtfCKEditorComparerParser($this->getObjectIt());
                $content .= '<div class="reset wysiwyg">';
                $diffBuilder = new WikiHtmlDiff(
                    $parser->parse($changeIt->getHtmlDecoded('Content')),
                    $parser->parse($freshContent)
                );
                $content .= $diffBuilder->build();
                $content .= '</div>';
            }
            else {
                $content = $changeIt->getHtmlDecoded('Content');
            }
        }
        else {
            $content = $object_it->getHtmlDecoded('Content');
        }

        $content .= '<div class="wiki-page-help">'.sprintf(text(2332), '<a target="_blank" href="'.$info['url'].'">'.$info['uid'].'</a>').'</div>';
        return $content;
    }
}
