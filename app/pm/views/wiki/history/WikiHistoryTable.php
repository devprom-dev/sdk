<?php
include_once SERVER_ROOT_PATH."pm/views/communications/ProjectLogTable.php";
include "WikiHistoryList.php";

class WikiHistoryTable extends ProjectLogTable
{
    private $pageObject = null;
	
	public function __construct( $object )
	{
        $this->pageObject = $object;
		parent::__construct(getFactory()->getObject('ChangeLog'));
	}
	
	function buildObjectIt()
	{
		$object_it = $this->pageObject->getExact($_REQUEST[strtolower(get_class($this->pageObject))]);

		if ( $object_it->getId() > 0 && $object_it->get('ParentPage') == '' ) {
			$object_it = getFactory()->getObject(get_class($object_it->object))
                ->getRegistry()->Query(
				    array (
				        new ParentTransitiveFilter($object_it->getId()),
                        new SortDocumentClause()
                    )
				);
		}

		return $object_it;
	}
	
	function getList()
	{
		return new WikiHistoryList( $this->getObject() );
	}
	
	function getFilters()
	{
		$filters = parent::getFilters();
		
		foreach( $filters as $key => $filter )
		{
			if ( in_array($filter->getValueParm(), array('requirement','object')) )
			{
				unset($filters[$key]);
			}
		}
		
		return array_merge( 
				array (
						new WikiFilterHistoryFormattingWebMethod()
				),
				$filters
		);
	}

    function buildStartFilter()
    {
        $filter = new ViewStartDateWebMethod();
        $filter->setDefault('');
        return $filter;
    }

	function getFilterPredicates()
	{
		$object_it = $this->getObjectIt();

		$predicates = array();
		if ( $object_it->get('ParentPage') != '' ) {
            $predicates[] = new FilterAttributeNotNullPredicate('Content');
        }

		return array_merge(
		    array_filter(
				parent::getFilterPredicates(),
				function($predicate) {
					return !$predicate instanceof ChangeLogVisibilityFilter;
				}
		    ),
            $predicates
        );
	}

    function getNewActions() {
        $version_url = $this->getObjectIt()->getPageVersions();
        if ( $version_url == '' ) return array();
        return array(
            array (
                'url' => $version_url,
                'name' => text(2237)
            )
        );
    }

	function getActions()
	{
		return array();
	}

	function getExportActions()
	{
		return array();
	}

	function getRenderParms( $parms )
	{
        $uid = new ObjectUID();
		return array_merge(
            parent::getRenderParms( $parms ),
            array (
                'navigation_url' => $this->getObjectIt()->getViewUrl(),
                'nearest_title' => $uid->getUidWithCaption($this->getObjectIt())
            )
        );
	}

	function getCaption() {
        return text(2238);
    }

    function getDefaultRowsOnPage() {
        return 5;
    }

    function buildQuickReports()
    {
    }
}