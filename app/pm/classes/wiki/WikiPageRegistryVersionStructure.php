<?php
include_once SERVER_ROOT_PATH.'core/classes/versioning/VersionedObject.php';

class WikiPageRegistryVersionStructure extends ObjectRegistrySQL
{
    public function setDocumentIt($document_it)
	{
		$this->document_it = $document_it->copy();
	}
	
	public function setSnapshotIt($snapshot_it)
	{
		$this->snapshot_it = $snapshot_it->copy();
	}
	
	function getQueryClause()
	{
		$versioned = new VersionedObject();
		$versionedIt = $versioned->getExact(get_class($this->getObject()));
 		$real_attributes = $versionedIt->get('Attributes');
	    $attributes = array();

	    foreach( $this->getObject()->getAttributes() as $attribute => $data )
	    {
	    	if ( in_array($attribute, $real_attributes) ) continue;
			if ( in_array($attribute, array('UID', 'DocumentId')) ) continue;
	        if ( $attribute != 'DocumentVersion' && !$this->getObject()->IsAttributeStored($attribute) ) continue;
	        
	        $attributes[] = $attribute;
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

        $persister = new SnapshotItemValuePersister($this->snapshot_it->getId());
        $persister->setObject( $this->getObject() );
        $snapshotAttributes = array_merge(
            array_map(
                function($value) {
                    if ( strpos($value, ') Content') !== false ) {
                        return " NULL Content";
                    }
                    if ( strpos($value, ') Caption') !== false ) {
                        return " NULL Caption";
                    }
                    return str_replace('t.WikiPageId', 't.ObjectId', $value);
                },
                $persister->getSelectColumns( "t" )
            ),
            array_map(
                function($value) {
                    return "NULL as ".$value;
                },
                $attributes
            )
        );

        return " (SELECT t.WikiPageId, t.UID, t.DocumentId, t.VPD, t.RecordVersion, ".join(",",$sqlAttributes).
	    	   "	FROM WikiPage t ".
	    	   "   WHERE t.DocumentId = ".$this->document_it->getId().$sqlPredicate.
	    	   "   UNION ".
	    	   "  SELECT t.ObjectId, (SELECT p.UID FROM WikiPage p WHERE p.WikiPageId = t.ObjectId AND p.DocumentId = ".$this->document_it->getId()."), ".$this->document_it->getId().", ".
		       "		 t.VPD, NULL, ".join(",",array_merge($snapshotAttributes)).
	    	   "	FROM cms_SnapshotItem t ".
	    	   "   WHERE t.Snapshot = ".$this->snapshot_it->getId().
	    	   "     AND NOT EXISTS (SELECT 1 FROM WikiPage p ".
	    	   "					  WHERE p.DocumentId = ".$this->document_it->getId()." AND p.WikiPageId = t.ObjectId) ".  
	    	   "  ) ";
	}
	
	private $snapshot_it;
	private $document_it;
}