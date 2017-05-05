<?php
include SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';
include 'TextChangesChart.php';

class TextChangesChartTable extends PMPageTable
{
    var $errors = array();

    function getList( $mode = '' )
    {
        return new TextChangesChart( $this->getObject() );
    }

    function getFiltersDefault()
    {
    	return array('any');
    }
    
    function getFilters()
    {
        $filters = array (
        		$this->buildAuthorFilter(),
        );
        $filters[] = new ViewStartDateWebMethod(translate('Начало'));
        $filters[] = new ViewFinishDateWebMethod();
        return $filters;
    }
    
    function buildAuthorFilter()
    {
    	$filter = new FilterObjectMethod(getFactory()->getObject('ProjectUser'), translate('Автор'));
    	$filter->setHasNone(false);
    	return $filter;
    }

    function getFilterPredicates()
    {
        $values = $this->getFilterValues();
        return array_merge(
            array (
                new FilterModifiedAfterPredicate( $values['start'] ),
                new FilterModifiedBeforePredicate( $values['finish'] ),
        		new FilterAttributePredicate( 'Author', $values['subversionauthor'] ),
            ),
            $this->getPage()->getPredicates()
        );
    }

    function getNewActions()
    {
    	return array();
    }

    function getExportActions()
    {
        return array();
    }
}