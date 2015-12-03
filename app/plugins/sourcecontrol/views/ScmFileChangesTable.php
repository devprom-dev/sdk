<?php
include_once SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';
include 'ScmFileChangesChart.php';
include 'ScmProductivityChart.php';

class ScmFileChangesTable extends PMPageTable
{
    var $errors = array();

    function getList( $mode = '' )
    {
        switch( $_REQUEST['report'] )
        {
            case 'codeproductivity':
                return new ScmProductivityChart( $this->getObject() );
            default:
                return new ScmFileChangesChart( $this->getObject() );
        }
    }

    function getFiltersDefault()
    {
    	return array('any');
    }
    
    function getFilters()
    {
        $filters = array (
                new FilterObjectMethod(getFactory()->getObject('pm_Subversion')),
        		$this->buildAuthorFilter(),
        );
        if ( count($this->getListRef()->getIds()) < 1 ) {
            $filters[] = new ViewStartDateWebMethod(translate('Начало'));
            $filters[] = new ViewFinishDateWebMethod();
        }
        return $filters;
    }
    
    function buildAuthorFilter()
    {
    	$filter = new FilterObjectMethod(getFactory()->getObject('SubversionAuthor'), translate('Автор'));
    	$filter->setIdFieldName('ReferenceName');
    	$filter->setHasNone(false);
    	return $filter;
    }

    function getFilterPredicates()
    {
        $values = $this->getFilterValues();
        return array (
                new FilterModifiedAfterPredicate( $values['start'] ),
                new FilterModifiedBeforePredicate( $values['finish'] ),
                new SCMFileChangeHistoryPredicate( 'Repository', $values['subversion'] ),
        		new SCMFileChangeHistoryPredicate( 'Author', $values['subversionauthor'] ),
        );
    }

    function getNewActions()
    {
    	return array();
    }
}