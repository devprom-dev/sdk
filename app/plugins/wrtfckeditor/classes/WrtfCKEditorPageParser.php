<?php
include_once SERVER_ROOT_PATH."pm/views/wiki/parsers/WikiParser.php";
include_once SERVER_ROOT_PATH . "pm/views/wiki/diff/WikiHtmlDiff.php";
define( 'REGEX_MATH_TEX', '/<span\s+class="math-tex"\s*>([^<]+)<\/span>/i' );

class WrtfCKEditorPageParser extends WikiParser
{
    private $tableRowIndex = 0;
    private $codeBlocks = array();

    function parse( $content = null )
    {
        $this->tableRowIndex = 0;

        $callbacks = array(
            CODE_ISOLATE => array($this, 'codeIsolate'),
            REGEX_UPDATE_UID => array($this, 'parseUpdateUidCallback'),
            REGEX_UID => array($this, 'parseUidCallback'),
            '/\s+src="([^d][^"]+)"/i' => array($this, 'parseImageSrcCallback')
        );

        $customUIDRule = $this->getObjectIt()->object->getDefaultAttributeValue('UID');
        if ( $customUIDRule != '' ) {
            $customUIDRule = preg_replace('/\\\{(ИД|Id|Номер|Number)\\\}/', '\d+', preg_quote($customUIDRule));
            $callbacks['/(^|<[^as][^>]*>|<s[^t][^>]*>|[^>\[\/A-Z0-9])\[?('.$customUIDRule.')\]?/mi'] = array($this, 'parseUidCallback');
        }
        $callbacks[CODE_RESTORE] = array($this, 'codeRestore');

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

    function parseMathTex( $match ) {
        $url = defined('MATH_TEX_IMG') ? MATH_TEX_IMG : 'http://latex.codecogs.com/gif.latex?';
        return '<img src="'.$url.rawurlencode(trim(html_entity_decode($match[1], ENT_QUOTES | ENT_HTML401, APP_ENCODING ))).'">';
    }

    function codeIsolate( $match ) {
        return '<code'.$match[1].'>'.array_push($this->codeBlocks, $match[2]).'</code>';
    }

    function codeRestore( $match ) {
        return '<code'.$match[1].'>'.$this->codeBlocks[$match[2] - 1].'</code>';
    }


}