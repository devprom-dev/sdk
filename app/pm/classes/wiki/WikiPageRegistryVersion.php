<?php
include_once SERVER_ROOT_PATH.'core/classes/versioning/VersionedObject.php';

class WikiPageRegistryVersion extends ObjectRegistrySQL
{
    private $comparisonMode = false;

    function getPersisters()
    {
        return array_merge(
            parent::getPersisters(),
            array(
                new WikiPageDetailsPersister()
            )
        );
    }

    public function setDocumentIt($documentIt) {
        $this->document_it = $documentIt;
    }

	public function setSnapshotIt($snapshot_it) {
		$this->snapshot_it = $snapshot_it->copy();
	}

	public function setComparisonMode( $mode = true ) {
        $this->comparisonMode = $mode;
    }

	function getQueryClause(array $parms)
	{
	    $attributes = array();
	    $stub_attributes = array();
        $versioned = new VersionedObject();
        $versionedIt = $versioned->getExact(get_class($this->getObject()));
        $real_attributes = $versionedIt->get('Attributes');
        $syntethicAttributes = array('Caption','Content');

	    foreach( $this->getObject()->getAttributes() as $attribute => $data )
	    {
            if ( !$this->getObject()->IsAttributeStored($attribute) ) {
                $syntethicAttributes[] = $attribute;
                continue;
            }
	    	if ( in_array($attribute, $real_attributes) ) continue;
			if ( in_array($attribute, array('UID', 'DocumentId', 'DocumentVersion', 'DataHash')) ) continue;

	        $attributes[] = $attribute;
            $stub_attributes[] = "NULL as ".$attribute;
	    }
	
        $sqlPredicate = '';
        foreach( $this->extractPredicates($parms) as $filter )
        {
            if ( $filter instanceof FilterInPredicate ) {
                $filter->setAlias('t');
                $filter->setObject( $this->getObject() );
                $sqlPredicate .= $filter->getPredicate();
            }
        }

        $persister = new SnapshotItemValuePersister($this->snapshot_it->getId());
        $persister->setObject( $this->getObject() );
        $columns = $persister->getSelectColumns( "p" );
        $real_attributes = array_merge(
            array_map(
                function($value) {
                    return "t.".$value;
                },
                $attributes
            ),
            array_map(
                function($value) use($syntethicAttributes) {
                    return in_array($value, $syntethicAttributes) ? ' NULL as '.$value : 't.'.$value;
                },
                $real_attributes
            )
        );

        $attributes = array_map(
                        function($value) {
                            return 't.'.$value;
                        }, $attributes);

        if ( count($columns) > 0 ) {
            $attributes = array_merge( $attributes, $columns);
            $stub_attributes = array_merge( $stub_attributes, $columns);
        }

	    return " (SELECT t.WikiPageId, t.UID, t.DocumentId, t.DocumentVersion, i.DataHash, t.VPD, t.RecordVersion, IF(t.DataHash = i.DataHash, 0, 1) IsModified, ".join(",",$attributes).
	    	   "	FROM WikiPage t, cms_SnapshotItem i, WikiPage p ".
	    	   "   WHERE t.DocumentId = " . $this->document_it->getId() . $sqlPredicate .
               "     AND i.Snapshot = ".($this->snapshot_it->getId() > 0 ? $this->snapshot_it->getId() : '0')." AND i.ObjectId = p.WikiPageId AND p.UID = t.UID ".
	    	   "   UNION ".
            ($this->comparisonMode ?
               "  SELECT t.WikiPageId, t.UID, t.DocumentId, t.DocumentVersion, t.DataHash, t.VPD, t.RecordVersion, 1, ".join(",",$real_attributes).
	    	   "	FROM WikiPage t ".
	    	   "   WHERE t.DocumentId = " . $this->document_it->getId() . $sqlPredicate .
               "     AND NOT EXISTS (SELECT 1 FROM cms_SnapshotItem i, WikiPage p ".
               "                      WHERE i.Snapshot = ".($this->snapshot_it->getId() > 0 ? $this->snapshot_it->getId() : '0')." AND i.ObjectId = p.WikiPageId AND p.UID = t.UID)".
	    	   "   UNION " : "").
	    	   "  SELECT p.WikiPageId, (SELECT t.UID FROM WikiPage t WHERE p.WikiPageId = t.WikiPageId), ".$this->document_it->getId().", '".$this->document_it->get('DocumentVersion')."', ".
		       "		 p.DataHash, p.VPD, NULL, 1, ".join(",",$stub_attributes).
	    	   "	FROM (SELECT t.ObjectId WikiPageId, t.VPD, t.DataHash FROM cms_SnapshotItem t WHERE t.Snapshot = ".($this->snapshot_it->getId() > 0 ? $this->snapshot_it->getId() : '0').") p ".
	    	   "   WHERE NOT EXISTS (SELECT 1 FROM WikiPage p1, WikiPage p2 ".
               "					  WHERE p1.DocumentId = ".$this->document_it->getId()." AND p1.UID = p2.UID AND p2.WikiPageId = p.WikiPageId) ".
	    	   "  ) ";
	}
	
	private $snapshot_it;
    private $document_it;
}