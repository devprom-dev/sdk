<?php
include_once SERVER_ROOT_PATH."ext/locale/LinguaStemRu.php";

class FilterSearchAttributesPredicate extends FilterPredicate
{
	private $attributes = array();

	function __construct( $phrase, $attributes )
	{
		$this->attributes = $attributes;
		parent::__construct($phrase);
	}
		
 	function _predicate( $filter )
 	{
		$predicates = array();
		foreach( $this->attributes as $attribute ) {
			if ( $this->getObject()->getAttributeType($attribute) == '') continue;
			if ( $attribute == 'Content' && !$this->getObject()->getRegistry() instanceof WikiPageRegistryVersion ) {
				$words = array_map(
					function($word) {
						if ( $word[0] != '+' && $word[0] != '-' ) $word = '+'.trim($word,'+-').'*';
						return $word;
					},
					SearchRules::getSearchItems($filter, getSession()->getLanguageUid())
				);
				$predicates[] = " MATCH (".$this->getAlias().".".$attribute.") AGAINST ('".join(' ',$words)."' IN BOOLEAN MODE) ";
			}
			else {
                $searchString = DAL::Instance()->Escape($filter);
                if ( $attribute == $this->getObject()->getIdAttribute() ) {
                    $searchString = array_pop(preg_split('/-/',$searchString));
                }
                $searchString = preg_replace('/%/', '', $searchString);
				$predicates[] = $this->getAlias().".".$attribute." LIKE ('%".$searchString."%') ";
			}
		}
		return count($predicates) < 1 ? " AND 1 = 2 " : " AND (".join(' OR ', $predicates).") ";
 	}
}
