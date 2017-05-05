<?php

include_once SERVER_ROOT_PATH."pm/views/communications/ProjectLogTable.php";

include "WikiHistorySettingBuilder.php";
include "WikiHistoryList.php";

class WikiHistoryTable extends ProjectLogTable
{
    private $pageObject = null;
	
	public function __construct( $object )
	{
        $this->pageObject = $object;
		parent::__construct(getFactory()->getObject('ChangeLog'));
        getSession()->addBuilder( new WikiHistorySettingBuilder($this->getObjectIt()) );
	}
	
	function buildObjectIt()
	{
		$object_it = $this->pageObject->getExact($_REQUEST[strtolower(get_class($this->pageObject))]);

		if ( $object_it->getId() > 0 && $object_it->get('ParentPage') == '' ) {
			$object_it = getFactory()->getObject(get_class($object_it->object))
                ->getRegistry()->Query(
				    array (
				        new WikiRootTransitiveFilter($object_it->getId())
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
		if ( $object_it->get('ParentPage') == '' ) return parent::getFilterPredicates();

		return array_merge(
		    array_filter(
				parent::getFilterPredicates(),
				function($predicate) {
					return !$predicate instanceof ChangeLogVisibilityFilter;
				}
		    ),
            array(
                new FilterAttributeNotNullPredicate('Content')
            )
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
		$page_it = $this->getObjectIt();
		$parms = array_merge(
		    $parms,
            array (
                'navigation_title' => $page_it->getDisplayName(),
                'navigation_url' => $page_it->getViewUrl(),
                'title' => text(2238)
            )
        );
		return parent::getRenderParms( $parms );
	}
} 