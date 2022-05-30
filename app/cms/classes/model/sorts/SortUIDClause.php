<?php

class SortUIDClause extends SortKeyClause
{
 	function clause()
 	{
 	    if ( in_array($this->getObject()->getEntityRefName(), array('WikiPage')) ) {
            return " IFNULL({$this->setColumnAlias("UID")},{$this->getObject()->getIdAttribute()}) {$this->getDirection()} ";
        }
 		return parent::clause();
 	}
}
