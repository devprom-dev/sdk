<?php

class TextUtils
{
    public static function breakLongWords( $content, $maxLength = 80 ) {
        return join(' ', array_map(
            function($line) use ($maxLength) {
                return mb_strlen($line) > $maxLength && strpos($line, 'src="') === false && strpos($line, 'href="') === false
                    ? join('<', array_map(
                        function($line) use ($maxLength) {
                            return TextUtils::mb_wordwrap($line, $maxLength, " ", true);
                        },
                        preg_split('/</u', $line)
                      ))
                    : $line;
            },
            preg_split('/\s+/u', $content)
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
}