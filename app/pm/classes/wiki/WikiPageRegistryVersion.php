<?php

include_once SERVER_ROOT_PATH.'core/classes/versioning/VersionedObject.php';

class WikiPageRegistryVersion extends ObjectRegistrySQL
{
	public function setDocumentIt($document_it)
	{
		$this->document_it = $document_it;
	}
	
	public function setSnapshotIt($snapshot_it)
	{
		$this->snapshot_it = $snapshot_it;
	}
	
	function getQueryClause()
	{
		$versioned = new VersionedObject();
 		
 		$real_attributes = $versioned->getExact(get_class($this->getObject()))->get('Attributes');
 		
	    $attributes = array();
	    
	    $stub_attributes = array();
	    
	    foreach( $this->getObject()->getAttributes() as $attribute => $data )
	    {
	    	if ( in_array($attribute, $real_attributes) ) continue;
			if ( in_array($attribute, array('UID')) ) continue;
	        if ( $attribute != 'DocumentVersion' && !$this->getObject()->IsAttributeStored($attribute) ) continue;
	        
	        $attributes[] = $attribute;
	        $stub_attributes[] = "NULL as ".$attribute;
	    }        
	
	    $select_attributes = array();
	    
	    foreach( $real_attributes as $attribute )
	    {
	    	if ( in_array($attribute, array('Caption', 'Content')) )
	    	{
	    		// skip overriden values to have comparison logic to be worked properly
	    		$select_attributes[] = "NULL as ".$attribute;
	    	}
	    	else
	    	{
	    		// use overriden values to have working predicates  
		    	$select_attributes[] = 
		    		"(SELECT iv.Value FROM cms_SnapshotItemValue iv ".
		    		"  WHERE iv.SnapshotItem = t.cms_SnapshotItemId ".
		    		"	 AND iv.ReferenceName = '".$attribute."') as ".$attribute;
	    	}
	    }

        $sqlPredicate = '';
        foreach( $this->getFilters() as $filter )
        {
            if ( $filter instanceof FilterInPredicate ) {
                $filter->setAlias('t');
                $filter->setObject( $this->getObject() );
                $sqlPredicate .= $filter->getPredicate();
            }
        }
	    $sqlAttributes = array_map(
	        function($value) {
                return "t.".$value;
            },
            array_merge($real_attributes, $attributes)
        );

	    return " (SELECT t.WikiPageId, t.UID, t.VPD, t.RecordVersion, ".join(",",$sqlAttributes).
	    	   "	FROM WikiPage t ".
	    	   "   WHERE t.DocumentId = ".$this->document_it->getId().$sqlPredicate.
	    	   "   UNION ".
	    	   "  SELECT t.ObjectId, (SELECT p.UID FROM WikiPage p WHERE p.WikiPageId = t.ObjectId AND p.DocumentId = ".$this->document_it->getId()."), ".
		       "		 t.VPD, NULL, ".join(",",array_merge($select_attributes, $stub_attributes)).
	    	   "	FROM cms_SnapshotItem t ".
	    	   "   WHERE t.Snapshot = ".$this->snapshot_it->getId().
	    	   "     AND NOT EXISTS (SELECT 1 FROM WikiPage p ".
	    	   "					  WHERE p.DocumentId = ".$this->document_it->getId()." AND p.WikiPageId = t.ObjectId) ".  
	    	   "  ) ";
	}
	
	private $snapshot_it;
	private $document_it;
}