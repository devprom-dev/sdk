<?php

include 'BlogIterator.php';

class Blog extends Metaobject
{
    function Blog() 
    {
        parent::Metaobject('Blog');
    }
    
    function createIterator() 
    {
        return new BlogIterator( $this );
    }
    
    function getDisplayName()
    {
        return translate('Блог проекта');
    }
}
