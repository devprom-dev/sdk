<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

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
        return preg_replace('/(?:<|&lt;)\/?([a-zA-Z]+) *[^<\/]*?(?:>|&gt;)/', '', self::removeHtmlEntities($text));
    }

    public static function getCleansedHtml( $body )
    {
        $body = self::_getCleansedHtml(
            self::_getCleansedHtml(
                $body,
                array(
                    '/<!--/', '/-->/',
                )
            ),
            array(
                '/<link[^>]*>/i',
                '/<\/link>/i',
                '/<script[^>]*>/i',
                '/<\/script>/i',
                '/<style[^>]*>/i',
                '/<\/style>/i',
                '/<base[^>]*>/i',
                '/<\/base>/i'
            )
        );

        $body = preg_replace(
            array(
                '/<o:[A-Za-z]>/',
                '/<\/o:[A-Za-z]>/'
            ),
            array (
                '',
                ''
            ), $body);

        return $body;
    }

    protected function _getCleansedHtml( $body, array $tags )
    {
        $replaceTags = array();
        foreach( array_keys($tags) as $index ) {
            $replaceTags[] = $index % 2 == 0 ? '[skip-style]' : '[/skip-style]';
        }
        $body = preg_replace( $tags, $replaceTags, $body);

        $lines = preg_split('/\[skip\-style\]/i', $body);
        $cleansedBody = array_shift($lines);
        foreach( $lines as $line ) {
            $parts = preg_split('/\[\/skip\-style\]/i', $line);
            $cleansedBody .= array_pop($parts);
        }
        return $cleansedBody;
    }

    public static function getValidHtml( $body )
    {
        $text = preg_replace('/<meta\s+[^>]+>/i', '', $body);
        if ( mb_stripos($text, '<body>') === false ) {
            $text = '<body>'.$text.'</body>';
        }
        else {
            $text = array_pop(preg_split('/<body>/i', $text));
            $text = array_shift(preg_split('/<\/body>/i', $text));
            $text = '<body>'.$text.'</body>';
        }
        $text = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?>'.$text;

        $was_state = libxml_use_internal_errors(true);
        $doc = new \DOMDocument("1.0", APP_ENCODING);
        if ( $doc->loadHTML($text) ) {
            $bodyElement = $doc->getElementsByTagName('body');
            if ( $bodyElement->length > 0 ) {
                $text = $doc->saveHTML($bodyElement->item(0));
                $body = preg_replace(
                    array(
                        '/<tr>[\s\r\n]*<\/tr>/i',
                        '/<tr>[\s\r\n]*<table/i',
                        '/<\/table>[\s\r\n]*<\/tr>/i',
                        '/<\/?body>/i'
                    ),
                    array (
                        '<tr><td></td></tr>',
                        '<tr><td><table',
                        '</table></td></tr>',
                        ''
                    ), $text);
            }
            else {
                $body = htmlentities($text);
            }
        }
        else {
            $body = htmlentities($text);
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
        return html_entity_decode( $text, ENT_QUOTES | ENT_HTML401, APP_ENCODING );
    }

    public function getAlphaNumericString( $text ) {
        $text = preg_replace( "/[^\p{L}|\p{N}\+\-\.,:;_\{\}]+/u", " ", $text );
        return preg_replace( "/[\p{Z}]{2,}/u", " ", $text );
    }

    public function getFileSafeString( $text ) {
        return preg_replace('/\s+/', '_', self::getAlphaNumericString($text));
    }

    public static function getWords( $text, $wordsCount = 1 ) {
        $items = preg_split('/\s+/', $text);
        $result = join(' ', array_slice($items, 0, $wordsCount));
        return $result != $text ? $result . '...' : $result;
    }

    public static function encodeImage( $filePath )
    {
        if ( file_exists(realpath($filePath)) ) {
            $maxImageWidth = 1024;
            if ( filesize($filePath) > 1048576 && class_exists('Imagick') ) {
                try {
                    $imagick = new Imagick(realpath($filePath));
                    $geometry = $imagick->getImageGeometry();
                    if ( $geometry['width'] > $maxImageWidth ) {
                        $imagick->scaleImage($maxImageWidth, 0, false);
                    }
                    return base64_encode($imagick->getImageBlob());
                }
                catch( Exception $e ) {
                    return base64_encode(file_get_contents($filePath));
                }
            }
            else {
                return base64_encode(file_get_contents($filePath));
            }
        }
        else {
            $curl = CurlBuilder::getCurl();
            curl_setopt($curl, CURLOPT_URL, $filePath);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($curl);
            curl_close($curl);
            return base64_encode($result);
        }
    }

    public static function parseIds( $text ) {
        return array_unique(
            array_filter(
                preg_split('/[,-]/', $text ),
                function($value) {
                    return is_numeric($value) && $value >= 0;
                }
            )
        );
    }

    public static function pathToUnixStyle($path) {
        return str_replace("\\", "/", realpath($path));
    }

    public static function getRandomPassword() {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password = substr(str_shuffle($chars), 0, 12);
        return $password;
    }
}