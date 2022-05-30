<?php
include "WikiPageComparableSnapshotRegistry.php";

class WikiPageComparableSnapshot extends Metaobject
{
	private $page_it;
	
	public function __construct( $page_it ) {
		$this->page_it = $page_it;
		parent::__construct('cms_Snapshot', new WikiPageComparableSnapshotRegistry($this) );
	}
	
	public function getDisplayName() {
		return text(1566);
	}
	
	public function getDocumentIt() {
		return $this->page_it;
	}
	
	public function getExact( $parms )
	{
	    if ( !is_array($parms) ) $parms = array($parms);
		$iterator = $this->getAll();
		foreach( $parms as $id ) {
            $iterator->moveToId( $id );
            if ( $iterator->getId() == $id ) return $iterator->copy();
        }
		return $this->getEmptyIterator();
	}
}