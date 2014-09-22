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
	    	
	        if ( !$this->getObject()->IsAttributeStored($attribute) ) continue;
	        
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
	    
	    return " (SELECT WikiPageId, VPD, RecordVersion, ".join(",",array_merge($real_attributes, $attributes)).
	    	   "	FROM WikiPage ".
	    	   "   WHERE ReferenceName = ".$this->getObject()->getReferenceName()." AND IsTemplate = 0 ".
	    	   "   UNION ".
	    	   "  SELECT t.ObjectId, t.VPD, NULL, ".join(",",array_merge($select_attributes, $stub_attributes)).
	    	   "	FROM cms_SnapshotItem t ".
	    	   "   WHERE t.Snapshot = ".$this->snapshot_it->getId().
	    	   "     AND NOT EXISTS (SELECT 1 FROM WikiPage p ".
	    	   "					  WHERE p.DocumentId = ".$this->document_it->getId()." AND p.WikiPageId = t.ObjectId) ".  
	    	   "  ) ";
	}
	
	private $snapshot_it;
	
	private $document_it;
}