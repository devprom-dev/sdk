<?php
include_once "FilterAttributePredicate.php";

class FilterAttributeHiePredicate extends FilterAttributePredicate
{
    function getQueryPredicate()
    {
        if ( !$this->getObject()->IsReference($this->getAttribute()) ) return parent::getQueryPredicate();

        $refObject = $this->getObject()->getAttributeObject($this->getAttribute());
        $hieAttributes = $refObject->getAttributesByGroup('hierarchy');
        if ( count($hieAttributes) < 1 ) return parent::getQueryPredicate();

        $sqls = array();
        foreach($this->getIds() as $value) {
            $sqls[] = " EXISTS (
                            SELECT 1 
                              FROM ".$refObject->getEntityRefName()." pa 
                             WHERE t.".$this->getAttribute()." = pa.".$refObject->getIdAttribute()." 
                               AND pa.ParentPath LIKE '%,".$value.",%') ";
        }

        return join(' OR ', $sqls);
 	}
}