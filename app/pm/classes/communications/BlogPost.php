<?php

include 'BlogPostIterator.php';
include "predicates/BlogPostDateFilter.php";
include "predicates/BlogPostTagsFilter.php";

class BlogPost extends Metaobject
{
    function BlogPost() 
    {
        parent::Metaobject('BlogPost');
        $this->defaultsort = 'RecordCreated DESC';
    }
    
    function createIterator() 
    {
        return new BlogPostIterator( $this );
    }
    
    function getPage()
    {
        return getSession()->getApplicationUrl($this).'project/blog?';
    }
}
