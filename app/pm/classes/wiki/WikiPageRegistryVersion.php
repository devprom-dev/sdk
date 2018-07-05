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

    public function setDocumentIt($document_it)
	{
		$this->document_it = $document_it->copy();
	}
	
	public function setSnapshotIt($snapshot_it)
	{
		$this->snapshot_it = $snapshot_it->copy();
	}

	public function setComparisonMode( $mode = true ) {
        $this->comparisonMode = $mode;
    }

	function getQueryClause()
	{
		$versioned = new VersionedObject();
		$versionedIt = $versioned->getExact(get_class($this->getObject()));
 		$real_attributes = $versionedIt->get('Attributes');
	    $attributes = array();
	    $stub_attributes = array();

	    foreach( $this->getObject()->getAttributes() as $attribute => $data )
	    {
	    	if ( in_array($attribute, $real_attributes) ) continue;
			if ( in_array($attribute, array('UID', 'DocumentId')) ) continue;
	        if ( $attribute != 'DocumentVersion' && !$this->getObject()->IsAttributeStored($attribute) ) continue;
	        
	        $attributes[] = $attribute;
            $stub_attributes[] = "NULL as ".$attribute;
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

        $persister = new SnapshotItemValuePersister($this->snapshot_it->getId());
        $persister->setObject( $this->getObject() );
        $columns = $persister->getSelectColumns( "t" );
        $real_attributes = array_merge(
            array_map(
                function($value) {
                    return "t.".$value;
                },
                $attributes
            ),
            array_map(
                function($value) {
                    return in_array($value, array('Caption','Content')) ? ' NULL as '.$value : 't.'.$value;
                },
                $real_attributes
            )
        );
        $attributes = array_merge(
            array_map(
                function($value) {
                    return 't.'.$value;
                },
                $attributes
            ), $columns);
        $stub_attributes = array_merge( $stub_attributes, $columns);

	    return " (SELECT t.WikiPageId, t.UID, t.DocumentId, t.VPD, t.RecordVersion, ".join(",",$attributes).
	    	   "	FROM WikiPage t ".
	    	   "   WHERE t.DocumentId = ".$this->document_it->getId().$sqlPredicate.
               "     AND EXISTS (SELECT 1 FROM cms_SnapshotItem i WHERE i.ObjectId = t.WikiPageId AND i.ObjectClass = '".$versionedIt->getId()."')".
	    	   "   UNION ".
            ($this->comparisonMode ?
               "  SELECT t.WikiPageId, t.UID, t.DocumentId, t.VPD, t.RecordVersion, ".join(",",$real_attributes).
	    	   "	FROM WikiPage t ".
	    	   "   WHERE t.DocumentId = ".$this->document_it->getId().$sqlPredicate.
               "     AND NOT EXISTS (SELECT 1 FROM cms_SnapshotItem i WHERE i.ObjectId = t.WikiPageId AND i.ObjectClass = '".$versionedIt->getId()."')".
	    	   "   UNION " : "").
	    	   "  SELECT t.WikiPageId, (SELECT p.UID FROM WikiPage p WHERE p.WikiPageId = t.WikiPageId AND p.DocumentId = ".$this->document_it->getId()."), ".$this->document_it->getId().", ".
		       "		 t.VPD, NULL, ".join(",",$stub_attributes).
	    	   "	FROM (SELECT t.ObjectId WikiPageId, t.VPD FROM cms_SnapshotItem t WHERE t.Snapshot = ".$this->snapshot_it->getId().") t ".
	    	   "   WHERE NOT EXISTS (SELECT 1 FROM WikiPage p ".
	    	   "					  WHERE p.DocumentId = ".$this->document_it->getId()." AND p.WikiPageId = t.WikiPageId) ".
	    	   "  ) ";
	}
	
	private $snapshot_it;
	private $document_it;
}