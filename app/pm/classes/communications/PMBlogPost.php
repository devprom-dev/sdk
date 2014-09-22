<?php

include 'BlogPost.php';
include 'PMBlogPostIterator.php';
include 'predicates/NewsBlogPostFilter.php';
include 'persisters/BlogPostTagsPersister.php';

class PMBlogPost extends BlogPost
{
    function __construct()
    {
        parent::BlogPost();
         
        $this->addAttribute( 'Tags', 'REF_BlogPostTagId', translate('в§уш'), false );
         
        $this->addPersister( new BlogPostTagsPersister() );
    }
     
    function createIterator()
    {
        return new PMBlogPostIterator( $this );
    }
}
