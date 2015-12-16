<?php

include_once SERVER_ROOT_PATH.'core/classes/versioning/VersionedObject.php';

class WikiPageRegistryBaseline extends ObjectRegistrySQL
{
	public function setDocumentIt($document_it)
	{
		$this->document_it = $document_it;
	}
	
	public function setBaselineIt($baseline_it)
	{
		$this->baseline_it = $baseline_it;
	}
	
	function getQueryClause()
	{
		$attributes = array();

	    foreach( $this->getObject()->getAttributes() as $attribute => $data )
	    {
	    	if ( in_array($attribute, array('Caption', 'Content')) ) continue;
	    	
	        if ( !$this->getObject()->IsAttributeStored($attribute) ) continue;
	        
	        $attributes[] = "t.".$attribute;
	    }        
		
	    return " (SELECT t.WikiPageId, t.VPD, t.RecordVersion, t.DocumentId, t.SortIndex, t.Caption, t.Content, ".join(",",$attributes).
	    	   "	FROM WikiPage t ".
	    	   "   WHERE t.ReferenceName = ".$this->getObject()->getReferenceName()." AND t.IsTemplate = 0 ".
	    	   "   UNION ".
	    	   "  SELECT t.WikiPageId, t.VPD, NULL, ".$this->document_it->getId().", 99999999999999, NULL, NULL, ".join(",",$attributes).
	    	   "	FROM WikiPage t ".
	    	   "   WHERE t.DocumentId = ".$this->baseline_it->getId().
	    	   "     AND NOT EXISTS (SELECT 1 FROM WikiPageTrace tr, WikiPage p ".
	    	   "					  WHERE tr.SourcePage = t.WikiPageId ".
	    	   "						AND tr.TargetPage = p.WikiPageId ".
	    	   "						AND tr.Type = 'branch' ".
	    	   "						AND p.DocumentId = ".$this->document_it->getId().
	    	   "					  UNION ".
	    	   "					 SELECT 1 FROM WikiPageTrace tr, WikiPage p ".
	    	   "					  WHERE tr.TargetPage = t.WikiPageId ".
	    	   "						AND tr.SourcePage = p.WikiPageId ".
	    	   "						AND tr.Type = 'branch' ".
	    	   "						AND p.DocumentId = ".$this->document_it->getId()." ) ".  
	    	   "  ) ";
	}
	
	private $baseline_it;
	
	private $document_it;
}