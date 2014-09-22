<?php

include "BlogPostDatesRegistry.php";

class BlogPostDates extends Metaobject
{
    function __construct() 
    {
        parent::__construct('entity', new BlogPostDatesRegistry($this));
    }
    
    function getDisplayName()
    {
    	return translate('Архив');
    }
}
