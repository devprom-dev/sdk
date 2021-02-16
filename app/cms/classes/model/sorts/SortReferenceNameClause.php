<?php

class SortReferenceNameClause extends SortAttributeClause
{
    private $reference = null;

    function __construct($reference, $attribute) {
        $this->reference = $reference;
        parent::__construct($attribute);
    }

 	function clause()
    {
        $sqls = array(
            " p.".$this->reference->getIdAttribute()." = ".$this->getColumn()
        );
        $vpds = $this->reference->getVpds();
        if ( count($vpds) > 1 ) {
            $sqls[] = " p.VPD IN ('".join("','", $vpds)."') ";
        }
		return " IFNULL((SELECT p.OrderNum 
		                   FROM ".$this->reference->getEntityRefName()." p 
		                  WHERE ".join(" AND ", $sqls)." LIMIT 1), 'z') ". $this->getSortType();
 	}
}
