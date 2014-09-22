<?php

class BlogPostFilePostFilter extends FilterPredicate
{
    function _predicate( $filter )
    {
        return " AND t.BlogPost = ".$filter;
    }
}
