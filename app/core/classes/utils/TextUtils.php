<?php

class TextUtils
{
    public static function breakLongWords( $content, $maxLength = 80 ) {
        return join(' ', array_map(
            function($line) use ($maxLength) {
                return mb_strlen($line) > $maxLength && strpos($line, 'src="') === false && strpos($line, 'href="') === false
                    ? join('<', array_map(
                        function($line) use ($maxLength) {
                            return TextUtils::mb_wordwrap($line, $maxLength, "<wbr/>", true);
                        },
                        preg_split('/</u', $line)
                      ))
                    : $line;
            },
            preg_split('/\040+/u', $content)
        ));
    }

    public static function mb_wordwrap($str, $width = 75, $break = "\n", $cut = false) {
        $lines = explode($break, $str);
        foreach ($lines as &$line) {
            $line = rtrim($line);
            if (mb_strlen($line) <= $width)
                continue;
            $words = explode(' ', $line);
            $line = '';
            $actual = '';
            foreach ($words as $word) {
                if (mb_strlen($actual.$word) <= $width)
                    $actual .= $word.' ';
                else {
                    if ($actual != '')
                        $line .= rtrim($actual).$break;
                    $actual = $word;
                    if ($cut) {
                        while (mb_strlen($actual) > $width) {
                            $line .= mb_substr($actual, 0, $width).$break;
                            $actual = mb_substr($actual, $width);
                        }
                    }
                    $actual .= ' ';
                }
            }
            $line .= trim($actual);
        }
        return implode($break, $lines);
    }

    public static function versionToString( $versionString ) {
        return join('',array_map(
            function ($value) {
                return str_pad($value, 6, "0", STR_PAD_LEFT);
            },
            array_pad(
                preg_split('/\./', $versionString), 4, "0"
            )
        ));
    }

    public static function removeHtmlEntities( $text ) {
        return html_entity_decode(
                    htmlentities(
                        str_replace("&nbsp;", " ",$text), ENT_COMPAT | ENT_HTML401, APP_ENCODING
                    ), ENT_COMPAT | ENT_HTML401, APP_ENCODING
                );
    }

    public static function stripAnyTags( $text ) {
        return strip_tags(self::removeHtmlEntities($text));
    }

    public static function getValidHtml( $body )
    {
        $text = preg_replace('/charset\s*=\s*[^"]+/i', 'charset=utf-8', $body);
        if ( mb_stripos($text, '<body>') === false ) {
            $text = '<body>'.$text.'</body>';
        }
        if ( mb_stripos($text, 'charset') === false ) {
            $text = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?>'.$text;
        }

        $was_state = libxml_use_internal_errors(true);
        $doc = new \DOMDocument("1.0", APP_ENCODING);
        if ( $doc->loadHTML($text) ) {
            $bodyElement = $doc->getElementsByTagName('body');
            if ( $bodyElement->length > 0 ) {
                $text = $doc->saveHTML($bodyElement->item(0));
                $body = preg_replace(
                    array(
                        '/<\/?body>/',
                        '/<style>/',
                        '/<\/style>/'
                    ),
                    array (
                        '',
                        '<styleSkipped>',
                        '</styleSkipped>'
                    ), $text);
            }
        }
        libxml_clear_errors();
        libxml_use_internal_errors($was_state);

        return $body;
    }

    public function EscapeShellArgument( $text ) {
        return preg_replace('/`/','\\`',trim(escapeshellarg($text),'"\''));
    }

    public function getXmlString( $text ) {
        return htmlentities(
            preg_replace("/[^\\x{0009}\\x{000A}\\x{000D}\\x{0020}-\\x{D7FF}\\x{E000}-\\x{FFFD}]/u", "",
                mb_convert_encoding(
                    $text, APP_ENCODING, APP_ENCODING
                ) // remove non-utf characters
            ), // remove non-xml characters
            ENT_XML1, APP_ENCODING, false
        ); // escape allowed UTF-characters
    }

    public function decodeHtml( $text ) {
        return trim(html_entity_decode( $text, ENT_QUOTES | ENT_HTML401, APP_ENCODING ));
    }

    public function getAlphaNumericString( $text ) {
        $text = preg_replace( "/[^\p{L}|\p{N}\-]+/u", " ", $text );
        return preg_replace( "/[\p{Z}]{2,}/u", " ", $text );
    }
}