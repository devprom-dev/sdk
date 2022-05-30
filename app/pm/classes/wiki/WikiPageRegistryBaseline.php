<?php
include_once SERVER_ROOT_PATH.'core/classes/versioning/VersionedObject.php';

class WikiPageRegistryBaseline extends ObjectRegistrySQL
{
	public function setDocumentIt($document_it) {
		$this->document_it = $document_it;
	}
	
	public function setBaselineIt($baseline_it) {
		$this->baseline_it = $baseline_it;
	}
	
	function getQueryClause(array $parms)
	{
		$attributes = array();

	    foreach( $this->getObject()->getAttributes() as $attribute => $data ) {
	    	if ( in_array($attribute, array('Caption', 'Content', 'DocumentVersion')) ) continue;
	        if ( !$this->getObject()->IsAttributeStored($attribute) ) continue;
	        
	        $attributes[] = "t.".$attribute;
	    }

        $sqlPredicate = '';
	    $filters = array_filter(
	        $this->extractPredicates($parms),
            function($filter) {
                return !$filter instanceof FilterVpdPredicate and !$filter instanceof FilterBaseVpdPredicate;
            });

        foreach( $filters as $filter ) {
            if ( $filter instanceof FilterInPredicate ) {
                $filter->setAlias('t');
                $filter->setObject( $this->getObject() );
                $sqlPredicate .= $filter->getPredicate();
            }
        }

	    return " (SELECT t.WikiPageId, t.VPD, t.RecordVersion, t.DocumentId, t.DocumentVersion, t.SortIndex, t.Caption, t.Content, ".join(",",$attributes).
	    	   "	FROM WikiPage t ".
	    	   "   WHERE t.DocumentId = ".$this->document_it->getId().$sqlPredicate.
	    	   "   UNION ".
	    	   "  SELECT t.WikiPageId, t.VPD, NULL, ".$this->document_it->getId().", '".$this->document_it->getHtmlDecoded('DocumentVersion')."', t.SortIndex, NULL, NULL, ".join(",",$attributes).
	    	   "	FROM WikiPage t ".
	    	   "   WHERE t.DocumentId = ".$this->baseline_it->getId().
	    	   "     AND NOT EXISTS (SELECT 1 FROM WikiPage p ".
	    	   "					  WHERE p.UID = t.UID ".
	    	   "						AND p.DocumentId = ".$this->document_it->getId()." ) ".
	    	   "  ) ";
	}

	private $baseline_it;
	private $document_it;
}