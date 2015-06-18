<?php

include "SearchForm.php";
include "SearchParameters.php";
        
class SearchPage extends PMPage
{
    function __construct()
    {
        parent::__construct();
        
        $this->addInfoSection( new SearchParameters() );
    }
    
 	function getTable() 
 	{
 		return null;
 	}
 	
 	function needDisplayForm()
 	{
 	    return true;
 	}
 	
 	function getForm() 
 	{
 	    global $model_factory;
 	    
 		return new SearchForm( $model_factory->getObject('entity') );
 	}
 
 	function getTitle()
 	{
 		return translate('Поиск');
 	}
}
