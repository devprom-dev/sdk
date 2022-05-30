<?php
include_once SERVER_ROOT_PATH.'core/classes/versioning/VersionedObject.php';

class WikiPageRegistryVersionStructure extends ObjectRegistrySQL
{
    public function setDocumentIt($documentIt) {
        $this->document_it = $documentIt;
    }

	public function setSnapshotIt($snapshot_it) {
		$this->snapshot_it = $snapshot_it->copy();
	}
	
	function getQueryClause(array $parms)
	{
		$versioned = new VersionedObject();
		$versionedIt = $versioned->getExact(get_class($this->getObject()));
 		$versionedAttributes = $versionedIt->get('Attributes');
	    $attributes = array();
        $syntethicAttributes = array();

        foreach( $this->getObject()->getAttributes() as $attribute => $data )
	    {
            if ( $attribute != 'DocumentVersion' && !$this->getObject()->IsAttributeStored($attribute) ) {
                $syntethicAttributes[] = $attribute;
                continue;
            }
	    	if ( in_array($attribute, $versionedAttributes) ) continue;
			if ( in_array($attribute, array('UID', 'DocumentId')) ) continue;
	        $attributes[] = $attribute;
	    }

        $sqlPredicate = '';
        foreach( $this->extractPredicates($parms) as $filter ) {
            if ( $filter instanceof FilterInPredicate ) {
                $filter->setAlias('t');
                $filter->setObject( $this->getObject() );
                $sqlPredicate .= $filter->getPredicate();
            }
        }

	    $sqlAttributes = array_map(
	        function($value) use($syntethicAttributes) {
                return in_array($value, $syntethicAttributes) ? ' NULL as '.$value : 't.'.$value;
            },
            array_merge($versionedAttributes, $attributes)
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
	    	   "   WHERE t.DocumentId = " . $this->document_it->getId() . $sqlPredicate .
	    	   "   UNION ".
	    	   "  SELECT t.ObjectId, (SELECT p.UID FROM WikiPage p WHERE p.WikiPageId = t.ObjectId), ".$this->document_it->getId().", ".
		       "		 t.VPD, NULL, ".join(",",array_merge($snapshotAttributes)).
	    	   "	FROM cms_SnapshotItem t ".
	    	   "   WHERE t.Snapshot = ".$this->snapshot_it->getId().
	    	   "     AND NOT EXISTS (SELECT 1 FROM WikiPage p1, WikiPage p2 ".
	    	   "					  WHERE p1.DocumentId = ".$this->document_it->getId()." AND p1.UID = p2.UID AND p2.WikiPageId = t.ObjectId) ".
	    	   "  ) ";
	}
	
	private $snapshot_it;
    private $document_it;
}