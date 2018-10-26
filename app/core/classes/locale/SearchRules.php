<?php
include "app-stopwords-russian.php";
include "app-stopwords-english.php";

class SearchRules
{
    static function getSearchItems( $text, $locale = 'en' )
    {
        $text = TextUtils::getAlphaNumericString($text);
        $stopwords = strtolower($locale) == 'en'
            ? APP_StopWords_English::stopwords()
            : APP_StopWords_Russian::stopwords();

        $stem = new Stem\LinguaStemRu();
        return array_map(
            function ($word) use ($stem) {
                if ( mb_strtoupper($word) == $word ) return $word;
                $stem = $stem->stem_word($word);
                return mb_strlen($stem) < 3 ? $word : $stem;
            },
            array_filter(
                preg_split('/\s+/', $text),
                function ($value) use ($stopwords) {
                    return !in_array($value, array('','+','-')) && !in_array($value, $stopwords);
                }
            )
        );
    }
}