<?php

class WikiPageModifierPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('Modifier');
    }

    function getSelectColumns( $alias )
 	{
 		$objectPK = $this->getPK($alias);

		$columns = array(
            " IFNULL(( SELECT c2.Author FROM WikiPageChange c2 WHERE c2.WikiPageChangeId = (SELECT MAX(c.WikiPageChangeId) FROM WikiPageChange c WHERE c.WikiPage = ".$objectPK." )), ".$alias.".Author) Modifier "
		);

 		return $columns;
 	}
}
