<?php

class ImportanceIterator extends CacheableIterator
{
 	function get( $attr ) {
 		switch( $attr ) {
 			case 'Caption':
 				return translate(parent::get($attr));
            case 'ColorAlpha':
                $alpha = 0.12;
                $rgbData = array_map(
                    function($value) use ($alpha) {
                        return min(round($value * $alpha + 255 * (1 - $alpha), 0), 255);
                    },
                    hex2rgb(parent::get('RelatedColor'))
                );
                return \ColorUtils::rgb2hex($rgbData);
            default:
 				return parent::get($attr);
 		}
 	}
}
