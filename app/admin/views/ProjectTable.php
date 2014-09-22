<?php

include ('ProjectList.php');

class ProjectTable extends PageTable
{
    function getSection()
    {
        return 'admin';
    }

	function getList()
	{
		return new ProjectList( $this->object );
	}

    function getCaption()
	{
		return '';
	}

	function getFilters()
	{
		return array(
			new FilterAutoCompleteWebMethod(
				$this->getObject(), translate('Название проекта') ),
		);
	}

	function getSortDefault( $sort_parm = 'sort' )
	{
	    if ( $sort_parm == 'sort' ) return 'Caption';
	    
	    return parent::getSortDefault( $sort_parm );
	}
	
	function getNewActions()
	{
		return array(
				array (
						'name' => translate('Создать'),
						'url' => '/projects/new'
				)
		);
	}
	
	function getDeleteActions()
	{
	    $actions = array();
	    
	    $method = new ProjectDeleteWebMethod();
	    
	    if ( $method->HasAccess() )
	    {
	        array_push( $actions, array (
    	        'url' => $method->getJSCall( $this->getObject() ),
    	        'name' => $method->getCaption()
	        ));
	    }
	     
	    return $actions;
	}
	
 	function getDefaultRowsOnPage()
	{
		return 60;
	}
}
