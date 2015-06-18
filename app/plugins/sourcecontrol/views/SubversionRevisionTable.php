<?php

include SERVER_ROOT_PATH.'pm/methods/c_date_methods.php';
include 'SubversionRevisionList.php';
include 'SubversionRevisionChart.php';

class SubversionRevisionTable extends PMPageTable
{
    var $errors = array();

    function getList( $mode = '' )
    {
        switch ( $mode )
        {
            case 'chart':
                return new SubversionRevisionChart( $this->object );

            default:
                return new SubversionRevisionList( $this->object );
        }
    }

    function getSubversionIt()
    {
        global $model_factory;

        $values = $this->getFilterValues();

        $repo = $model_factory->getObject('pm_Subversion');
        if ( $values['subversion'] > 0 )
        {
            $repo_it = $repo->getExact($values['subversion']);
        }
        else
        {
            $repo_it = $repo->getAll();
        }

        return $repo_it;
    }

    function getFiltersDefault()
    {
    	return array('any');
    }
    
    function getFilters()
    {
        global $model_factory;

        return array (
                new FilterObjectMethod( $model_factory->getObject('pm_Subversion') ),
        		$this->buildAuthorFilter(),
                new ViewStartDateWebMethod( translate('Начало'), true ),
                new ViewFinishDateWebMethod()
        );
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
                new SubversionRevisionRequirementPredicate( $_REQUEST['requirement'] ),
                new FilterSubmittedAfterPredicate( $values['start'] ),
                new FilterSubmittedBeforePredicate( $values['finish'] ),
                new FilterAttributePredicate( 'Repository', $values['subversion'] ),
        		new FilterAttributePredicate( 'Author', $values['subversionauthor'] ),
        );
    }

    function getSortDefault( $sort_parm )
    {
        if ( $sort_parm == 'sort' )
        {
            return 'CommitDate.D';
        }

        if ( $sort_parm == 'sort2' )
        {
            return 'Version.D';
        }

        return parent::getSortDefault( $sort_parm );
    }

    function getViewFilter()
    {
        return new ViewRevisionViewWebMethod();
    }

    function getNewActions()
    {
    	$actions = array();
    	
    	$connector = getFactory()->getObject('Subversion');
    	if ( $connector->getRegistry()->Count(array(new FilterVpdPredicate())) > 0 )
    	{
		    $job_it = getFactory()->getObject('co_ScheduledJob')->getByRef('ClassName', 'processrevisionlog');
		    $actions[] = array ( 
		            'name' => translate('Обновить'),
		    		'uid' => 'refresh-commits', 
		            'url' => '/tasks/command.php?class=runjobs&job='.$job_it->getId().
		    						'&chunk='.join(',',$connector->getAll()->idsToArray()).
		    						'&redirect='.urlencode($_SERVER['REQUEST_URI']) 
		    );
    	}
    	
		$method = new ObjectCreateNewWebMethod($connector);
		$method->setRedirectUrl('donothing');
	    $actions[] = array ( 
	            'name' => $connector->getDisplayName(),
	    		'uid' => 'new-repository', 
	            'url' => $method->getJSCall() 
	    );
	    return $actions;
    }
}