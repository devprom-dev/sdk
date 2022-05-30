<?php

class WikiPageCompareContentFilter extends FilterPredicate
{
    private $objectIt = null;

    function __construct( $filter, $objectIt )
    {
        parent::__construct($filter);
        $this->objectIt = $objectIt;
    }

    function _predicate( $filter )
 	{
 	    if ( $this->objectIt->getId() == '' ) return " AND 1 = 1 ";
 	    if ( $filter != 'modified' ) return " AND 1 = 1 ";

 	    if ( $this->objectIt->get('cms_SnapshotId') != '' ) {
            return
                " AND (NOT EXISTS (SELECT 1 FROM cms_SnapshotItem b, WikiPage p 
                  		       WHERE b.ObjectId = p.WikiPageId 
                		         AND p.UID = t.UID 
                		         AND b.Snapshot = {$this->objectIt->get('cms_SnapshotId')}
                                 AND b.DataHash = t.DataHash) 
                        OR t.RecordVersion IS NULL) ";
        }
        else {
            return
                " AND (NOT EXISTS (SELECT 1 FROM WikiPage p 
                  		       WHERE p.DocumentId = {$this->objectIt->get('DocumentId')}
                		         AND p.UID = t.UID 
                			     AND p.DataHash = t.DataHash)
                        OR t.RecordVersion IS NULL)";
        }
 	}
}
