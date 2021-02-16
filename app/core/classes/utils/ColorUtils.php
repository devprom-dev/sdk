<?php
include 'Coloring.php';

class ColorUtils
{
    public static function getTextStyle( $backgroundColor )
    {
        if ( strpos($backgroundColor, '#') === false ) return "";

        $text_rgb = array(255,255,255);
        $background_rgb = hex2rgb(trim($backgroundColor,'#'));

        if ( lumdiff($background_rgb, $text_rgb) < 2 ) {
            $text_rgb = array(
                max($background_rgb[0] / 3,0),
                max($background_rgb[1] / 3,0),
                max($background_rgb[2] / 3,0)
            );
        }
        return "text-shadow:none;color:".self::rgb2hex($text_rgb).";";
    }

    public static function rgb2hex( array $text_rgb ) {
        return '#'.str_pad(dechex($text_rgb[0]),2,"0").str_pad(dechex($text_rgb[1]),2,"0").str_pad(dechex($text_rgb[2]),2,"0");
    }

    public static function hex2rgb( $hexColor, $alpha = 0 ) {
        list($r, $g, $b) = sscanf($hexColor, "#%02x%02x%02x");
        return 'rgba(' . $r . ', ' . $g . ', ' . $b . ', ' . $alpha . ')';
    }
}
