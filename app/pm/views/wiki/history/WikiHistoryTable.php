<?php
include_once SERVER_ROOT_PATH."pm/views/communications/ProjectLogTable.php";
include_once SERVER_ROOT_PATH."pm/methods/WikiFilterHistoryFormattingWebMethod.php";
include "WikiHistoryList.php";

class WikiHistoryTable extends ProjectLogTable
{
    private $pageObject = null;
	
	public function __construct( $object )
	{
        $this->pageObject = $object;
		parent::__construct(getFactory()->getObject('ChangeLogAggregated'));
	}
	
	function buildObjectIt()
	{
		$object_it = $this->pageObject->getExact($_REQUEST[strtolower(get_class($this->pageObject))]);

		if ( $object_it->getId() > 0 && $object_it->get('ParentPage') == '' ) {
			$object_it = getFactory()->getObject(get_class($object_it->object))
                ->getRegistry()->Query(
				    array (
				        new ParentTransitiveFilter($object_it->getId()),
                        new FilterAttributePredicate('DocumentId', $object_it->get('DocumentId')),
                        new SortDocumentClause()
                    )
				);
		}

        if ( $object_it->getId() < 1 ) {
            $object_it = $object_it->object->getRootIt();
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
		
		foreach( $filters as $key => $filter ) {
			if ( in_array($filter->getValueParm(), array('requirement','object')) ) {
				unset($filters[$key]);
			}
		}

        $branchIt = getFactory()->getObject('WikiPageBaseline')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('ObjectId', $this->getObjectIt()->get('DocumentId')),
                new FilterAttributePredicate('ObjectClass', get_class($this->getObjectIt()->object))
            )
        );
		$filters[] = $this->buildToBaselineFilter($branchIt);
        $filters[] = $this->buildFromBaselineFilter($branchIt);

		return array_merge( 
            array (
            ),
            $filters
		);
	}

	function buildFromBaselineFilter($branchIt)
    {
        $filter = new FilterObjectMethod($branchIt->copyAll(), text(2914), 'frombaseline');
        $filter->setType('singlevalue');
        $filter->setHasAny(false);
        $filter->setHasNone(false);
        return $filter;
    }

    function buildToBaselineFilter($branchIt)
    {
        $filter = new FilterObjectMethod($branchIt->copyAll(), text(2915), 'tobaseline');
        $filter->setType('singlevalue');
        $filter->setHasAny(false);
        $filter->setHasNone(false);
        return $filter;
    }

    function buildStartFilter() {
        $filter = new ViewStartDateWebMethod();
        return $filter;
    }

	function getFilterPredicates( $values )
	{
		$predicates = array();

		$items = \TextUtils::parseFilterItems($values['frombaseline']);
		if ( count($items) > 0 ) {
            $branchIt = getFactory()->getObject('Snapshot')->getExact($items[0]);
            $predicates[] = new ChangeLogStartFilter(SystemDateTime::convertToClientTime($branchIt->get('RecordModified')));
        }

        $items = \TextUtils::parseFilterItems($values['tobaseline']);
        if ( count($items) > 0 ) {
            $branchIt = getFactory()->getObject('Snapshot')->getExact($items[0]);
            $predicates[] = new ChangeLogFinishFilter(SystemDateTime::convertToClientTime($branchIt->get('RecordModified')));
        }

		return array_merge(
		    array_filter(
				parent::getFilterPredicates( $values ),
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

    function getDetails()
    {
        return array();
    }
}