<?php

include 'BlogPostFileIterator.php';
include 'predicates/BlogPostFilePostFilter.php';

class BlogPostFile extends Metaobject
{
    var $post_it;

    function BlogPostFile($post_it = null) 
    {
        $this->post_it = $post_it;
        parent::Metaobject('BlogPostFile');
    }
    
    function createIterator() 
    {
        return new BlogPostFileIterator( $this );
    }
    
    function getPageNameObject( $object_id = '' )
    {
        $file = getFactory()->getObject('BlogPostFile');
        $file_it = $file->getExact($object_id);
        $post_it = $file_it->getRef('BlogPost');
        return $post_it->object->getPageNameEditMode($post_it->getId()).'&file_mode=edit&file_id='.$object_id;
    }

    function IsAttributeRequired( $attr_name )
    {
        if ( $attr_name == 'Content')
        {
            return true;
        }

        return parent::IsAttributeRequired( $attr_name );
    }
}