<?php
include_once SERVER_ROOT_PATH."pm/views/wiki/parsers/WikiParser.php";
define( 'REGEX_MATH_TEX', '/<span\s+class="math-tex"\s*>([^<]+)<\/span>/i' );

class WrtfCKEditorPageParser extends WikiParser
{
    function parse( $content = null )
    {
        $content = preg_replace_callback(REGEX_INCLUDE_PAGE, array($this, 'parseIncludePageCallback'), $content);
		$content = preg_replace_callback(REGEX_UID, array($this, 'parseUidCallback'), $content);
		$content = preg_replace_callback(REGEX_UPDATE_UID, array($this, 'parseUpdateUidCallback'), $content);
        $content = preg_replace_callback('/\s+src="([^"]*)"/i', array($this, 'parseImageSrcCallback'), $content);

        return $content;
    }

    function parseMathTex( $match ) {
        $url = defined('MATH_TEX_IMG') ? MATH_TEX_IMG : 'http://latex.codecogs.com/gif.latex?';
        return '<img src="'.$url.rawurlencode(trim(html_entity_decode($match[1], ENT_QUOTES | ENT_HTML401, APP_ENCODING ))).'">';
    }
}