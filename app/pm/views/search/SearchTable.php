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
            $this->buildFilterState()
		);
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

	function getFilterPredicates( $values )
	{
		return array_merge(
		    parent::getFilterPredicates( $values ),
			array (
				new FilterAttributePredicate( 'entityId', $values['artifacts'] ),
                new StateCommonPredicate($values['state'])
			)
		);
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
