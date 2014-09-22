<?php

include 'BlogArchiveSection.php';
include 'BlogTable.php';

class BlogPage extends PMPage
{
	function getObject()
	{
        return getFactory()->getObject('BlogPost');
	}
	
	function getTable()
    {
        return new BlogTable( $this->getObject() );
    }

    function needDisplayForm()
    {
        return $_REQUEST['entity'] == 'BlogPost' && $_REQUEST['BlogPostaction'] != 'view'; 
    }
    
    function getForm()
    {
        return new BlogForm( $this->getObject() );
    }
}