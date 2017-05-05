<?php

class UserTitleSortClause extends SortAttributeClause
{
	function __construct() {
		parent::__construct('Caption');
	}

	function clause() {
        if ( !is_object(getSession()) ) return parent::clause();
        $userId = getSession()->getUserIt()->getId();
        if ( $userId < 1 ) return parent::clause();
 		return " IF(".$this->getAlias().".cms_UserId = ".$userId.", '1', ".$this->getAlias().".Caption) ".$this->getSortType();
 	}
}
