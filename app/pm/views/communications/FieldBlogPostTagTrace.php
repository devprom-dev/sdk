<?php

include_once SERVER_ROOT_PATH.'pm/views/tags/FieldTagTrace.php';

class FieldBlogPostTagTrace extends FieldTagTrace
{
    function __construct( $anchor )
    {
        parent::__construct( $anchor, 'BlogPost' );
    }

    function getTagObject()
    {
        $tag = getFactory()->getObject('BlogPostTag');
        
        $anchor_it = $this->getAnchorIt();
        	
        $tag->addFilter(
                new FilterAttributePredicate( 'BlogPost',
                        is_object($anchor_it) ? $anchor_it->getId() : 0 ) );

        return $tag;
    }
}