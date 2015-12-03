<?php
include_once SERVER_ROOT_PATH."ext/locale/LinguaStemRu.php";

class FilterSearchAttributesPredicate extends FilterPredicate
{
	private $attributes = array();
	private $stem = null;
	
	function __construct( $phrase, $attributes )
	{
		$this->attributes = $attributes;
		$this->stem = new Stem\LinguaStemRu();
		parent::__construct($phrase);
	}
	
 	function _predicate( $filter )
 	{
		$stem = $this->stem;

 		$words = array_map(
			function($word) use($stem) {
				$word = $stem->stem_word($word);
				if ( $word[0] != '+' && $word[0] != '-' ) $word = '+'.$word;
				$word .= '*';
                return $word;
			},
			preg_split('/\s+/', $filter)
		);

 		return " AND MATCH (".join($this->attributes, ',').") AGAINST ('".join(' ',$words)."' IN BOOLEAN MODE) ";
 	}
}
