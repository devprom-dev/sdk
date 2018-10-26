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

            $searchString = $filter;
            if ( $this->getObject()->getAttributeType($attribute) == 'wysiwyg' ) {
                $searchString = htmlentities($searchString);
            }

			if ( $attribute == 'Content' && !$this->getObject()->getRegistry() instanceof WikiPageRegistryVersion ) {
				$words = array_map(
					function($word) {
                        return preg_replace('/@/', '*', trim(addslashes($word),'+-').'*');
					},
					SearchRules::getSearchItems($searchString, getSession()->getLanguageUid())
				);
				if ( count($words) > 0 ) {
                    $predicates[] = " MATCH (".$this->getAlias().".".$attribute.") AGAINST ('+".join(' ',$words)."' IN BOOLEAN MODE) ";
                }
			}
			else {
                if ( $attribute == $this->getObject()->getIdAttribute() ) {
                    $searchString = array_pop(preg_split('/-/',$searchString));
                }
                $searchString = preg_replace('/%/', '', $searchString);
				$predicates[] = $this->getAlias().".".$attribute." LIKE ('%".addslashes($searchString)."%') ";
			}
		}

		return count($predicates) < 1 ? " AND 1 = 2 " : " AND (".join(' OR ', $predicates).") ";
 	}
}
