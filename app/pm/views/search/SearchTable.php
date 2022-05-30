<?php
include SERVER_ROOT_PATH . 'pm/classes/search/SearchWordsMode.php';
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
            $this->buildFilterWordsMode()
		);
	}

	function buildTypeFilter() {
		$category = new FilterObjectMethod(
            getFactory()->getObject('SearchableObjectSet'), translate('Артефакты'), 'artifacts' );
		$category->setHasNone(false);
		return $category;
	}

	function buildFilterState() {
        $filter = new FilterObjectMethod(
            getFactory()->getObject('StateCommon'), translate('Состояние'), 'state' );
        $filter->setHasNone(false);
        $filter->setDefaultValue('N,I');
        return $filter;
    }

    function buildFilterWordsMode() {
        $filter = new FilterObjectMethod(
            new SearchWordsMode(), translate('Режим'), 'wordsmode' );
        $filter->setHasNone(false);
        $filter->setHasAll(false);
        $filter->setType('singlevalue');
        $filter->setDefaultValue('all');
        return $filter;
    }

    function buildSearchPredicate($values) {
        $predicate = parent::buildSearchPredicate($values);
        $predicate->setWordsMode($values['wordsmode']);
        return $predicate;
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
