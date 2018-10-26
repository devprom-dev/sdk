<?php
include "SearchList.php";

class SearchTable extends PMPageTable
{
	function getList() {
		return new SearchList( $this->getObject() );
	}

	function getFilters()
	{
		return array(
            $this->buildTypeFilter(),
            $this->buildFilterState(),
		    $this->buildSearchFilter()
		);
	}

	function buildSearchFilter() {
		$search = new FilterTextWebMethod( text(2246), 'search-keywords' );
		$search->setStyle( 'width:640px' );
		return $search;
	}

	function buildTypeFilter() {
		$category = new FilterObjectMethod( getFactory()->getObject('SearchableObjectSet'), translate('Артефакты'), 'artifacts' );
		$category->setHasNone(false);
		return $category;
	}

	function buildFilterState( $filterValues = array() ) {
        $filter = new FilterObjectMethod( getFactory()->getObject('StateCommon'), translate('Состояние'), 'state' );
        $filter->setHasNone(false);
        return $filter;
    }

	function getFilterPredicates()
	{
		$values = $this->getFilterValues();
		return array_merge(
			array (
				new FilterAttributePredicate( 'Caption', $values['search-keywords'] ),
				new FilterAttributePredicate( 'entityId', $values['artifacts'] ),
                new StateCommonPredicate($values['state'])
			)
		);
	}
	
	function getFiltersDefault() {
	    return array('search-keywords', 'artifacts', 'state');
	}
	
	function getSortFields() {
		return array();
	}
	
	function getSortDefault( $parm ) {
		return '';
	}
	
	function getNewActions() {
		return array();
	}

	function getExportActions() {
		return array();
	}

	function getBulkActions() {
		return array();
	}

	function IsFilterPersisted() {
	    return true;
    }

    function getCaption() {
        return translate('Поиск');
    }
}
