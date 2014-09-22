<?php

class WikiPageRegistry extends ObjectRegistrySQL
{
	function getQueryClause()
	{
	    $attributes = array();
	    
	    foreach( $this->getObject()->getAttributes() as $attribute => $data )
	    {
	        if ( $attribute == 'Content' ) continue;
	        if ( $attribute == 'UserField3' ) continue;
	        
	        if ( !$this->getObject()->IsAttributeStored($attribute) ) continue;
	        
	        $attributes[] = $attribute;
	    }        
	
	    if ( $this->getObject()->getReferenceName() != '' )
	    {
	    	$reference_predicate .= " AND ReferenceName = ".$this->getObject()->getReferenceName()." ";
	    }
	    
	    return " (SELECT WikiPageId, VPD, RecordVersion, ".join(",",$attributes).", IF(Content<>'', 'Y', 'N') ContentPresents, DocumentId, SortIndex ".
	    	   "	FROM WikiPage WHERE 1 = 1 ".$reference_predicate." AND IsTemplate = 0) ";
	}
}