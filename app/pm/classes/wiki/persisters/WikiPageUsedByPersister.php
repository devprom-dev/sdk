<?php

class WikiPageUsedByPersister extends ObjectSQLPersister
{
	function getAttributes() {
		return array('UsedBy');
	}

	function getSelectColumns( $alias )
 	{
 		return array(
			" (SELECT GROUP_CONCAT(CAST(ub.WikiPageId AS CHAR)) 
			     FROM WikiPage ub WHERE LOCATE(CONCAT('".get_class($this->getObject()).":',t.WikiPageId),ub.Dependency)) UsedBy "
		);
 	}
}