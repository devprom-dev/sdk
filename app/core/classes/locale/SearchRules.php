<?php

class SearchRules
{
    static function getSearchItems( $text )
    {
        $stem = new Stem\LinguaStemRu();
        return array_map(
            function ($word) use ($stem) {
                if ( mb_strtoupper($word) == $word ) return $word;
                $stem = $stem->stem_word($word);
                return mb_strlen($stem) < 3 ? $word : $stem;
            },
            array_filter(
                preg_split('/\s+/', $text),
                function ($value) {
                    return trim($value) != '';
                }
            )
        );
    }
}