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
                " AND NOT EXISTS (SELECT 1 FROM cms_SnapshotItem b, cms_SnapshotItemValue v " .
                "  		       WHERE b.ObjectId = t.WikiPageId " .
                "		         AND b.Snapshot = " .$this->objectIt->get('cms_SnapshotId').
                "			     AND v.SnapshotItem = b.cms_SnapshotItemId " .
                "			     AND v.ReferenceName = 'Content' ".
                "                AND v.Value = t.Content ) ";
        }
        else {
            return
                " AND NOT EXISTS (SELECT 1 FROM WikiPage p " .
                "  		       WHERE p.DocumentId = " . $this->objectIt->get('DocumentId').
                "		         AND p.UID = t.UID " .
                "			     AND p.Content = t.Content ) ";
        }
 	}
}
