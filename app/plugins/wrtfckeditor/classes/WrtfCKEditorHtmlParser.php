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

        if ( function_exists('preg_replace_callback_array') ) {
            return preg_replace_callback_array(
                array (
                    REGEX_MATH_TEX => array($this, 'parseMathTex'),
                    REGEX_IMAGE_NUMBERING => array($this, 'imageNumbering'),
                    REGEX_TABLE_NUMBERING => array($this, 'tableNumbering'),
                    '/(^|[^=]"|[^="])((http:|https:)\/\/([\w\.\/:\-\?\%\=\#\&\;\+\,\(\)\[\]]+[\w\.\/:\-\?\%\=\#\&\;\+\,]{1}))/im' => array($this, 'parseUrl')
                ),
                $content
            );
        }
        else {
            $content = preg_replace_callback(REGEX_MATH_TEX, array($this, 'parseMathTex'), $content);
            $content = preg_replace_callback(REGEX_IMAGE_NUMBERING, array($this, 'imageNumbering'), $content);
            $content = preg_replace_callback(REGEX_TABLE_NUMBERING, array($this, 'tableNumbering'), $content);
            $content = preg_replace_callback('/(^|[^=]"|[^="])((http:|https:)\/\/([\w\.\/:\-\?\%\=\#\&\;\+\,\(\)\[\]]+[\w\.\/:\-\?\%\=\#\&\;\+\,]{1}))/im', array($this, 'parseUrl'), $content);
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
}
