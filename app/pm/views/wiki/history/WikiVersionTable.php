<?php
include "WikiVersionList.php";

class WikiVersionTable extends PMPageTable
{
	private $pageIt = null;
    private $pageObject = null;

	public function __construct( $object ) {
	    $this->pageObject = $object;
		parent::__construct(getFactory()->getObject('Snapshot'));
	}
	
	function getPageIt()
	{
		if ( is_object($this->pageIt) ) $this->pageIt;
		return $this->pageIt = $this->buildPageIt();
	}
	
	function buildPageIt()
	{
	    $ids = TextUtils::parseIds($_REQUEST['page']);
        if ( count($ids) < 1 ) return $this->pageObject->getEmptyIterator();

        return $this->pageObject->getRegistry()->Query(
            array (
                new ParentTransitiveFilter($ids)
            )
        );
	}

	function getList() {
		return new WikiVersionList( $this->getObject() );
	}
	
	function getFilters() {
		return array();
	}
	
	function getFilterPredicates()
	{
		return array_merge(
            parent::getFilterPredicates(),
            array (
                new FilterAttributePredicate('ObjectClass', get_class($this->pageObject)),
                new FilterAttributePredicate('ObjectId', $this->getPageIt()->fieldToArray('DocumentId')),
                new FilterHasNoAttributePredicate('Type', 'branch')
            )
		);
	}
	
	function getActions() {
		return array();
	}

	function getNewActions() {
        return array(
            array (
                'url' => $this->getPageIt()->getHistoryUrl(),
                'name' => text(2238)
            )
        );
    }

    function getExportActions() {
		return array();
	}

	function getSortFields() {
        return array();
    }

    function getSortAttributeClause($field) {
        return new SortReverseKeyClause();
    }

    function getRenderParms( $parms )
	{
        if ( $this->getPageIt()->count() > 1 ) {
            $titleParms = array();
        }
        else {
            $titleParms = array (
                'navigation_title' => $this->getPageIt()->getDisplayName(),
                'navigation_url' => $this->getPageIt()->getViewUrl(),
                'title' => text(2235)
            );
        }
		return array_merge(
            parent::getRenderParms( $parms ),
            $titleParms
		);
	}
}