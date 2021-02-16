<?php

class WikiTagsPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias ) {
 		return array(
            "(SELECT GROUP_CONCAT(wt.Tag) FROM WikiTag wt WHERE wt.Wiki = ".$this->getPK($alias)." ) Tags "
        );
 	}
}
