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

    function check( $filter ) {
        return addslashes($filter);
    }

 	function _predicate( $filter )
 	{
		$predicates = array();
		foreach( $this->attributes as $attribute ) {
			if ( $this->getObject()->getAttributeType($attribute) == '') continue;

            $searchString = trim($this->getObject()->formatValueForDb($attribute, trim($filter)), "'");
            if ( $searchString == '' ) continue;

            if ( $this->getObject()->getAttributeType($attribute) == 'wysiwyg' ) {
                $searchString = htmlentities($searchString);
            }

            $skipMatch = $this->getObject()->getRegistry() instanceof WikiPageRegistryVersion
                || $this->getObject()->getEntityRefName() == 'ObjectChangeLog';

			if ( $attribute == 'Content' && !$skipMatch ) {
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

        $clauses = array();
        foreach( SearchRules::getSearchItems($filter, getSession()->getLanguageUid()) as $word ) {
            $clauses[] = " av.StringValue LIKE '%".addslashes($word)."%' OR av.TextValue LIKE '%".addslashes($word)."%' ";
        }

        if ( count($clauses) > 0 ) {
            $predicates[] = " EXISTS (SELECT 1 FROM pm_CustomAttribute at, pm_AttributeValue av
                                      WHERE at.EntityReferenceName = '".strtolower(get_class($this->getObject()))."'
                                        AND at.pm_CustomAttributeId = av.CustomAttribute
                                        AND av.ObjectId = t.".$this->getObject()->getIdAttribute()."
                                        AND (".join(" OR ", $clauses).") ) ";
        }

        return count($predicates) < 1 ? " AND 1 = 2 " : " AND (".join(' OR ', $predicates).") ";
 	}
}
