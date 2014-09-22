<?php

class BlogPostTagsFilter extends FilterPredicate
{
    function _predicate( $filter )
    {
        global $model_factory;

        $tag = $model_factory->getObject('Tag');
        
        $tag_it = $tag->getExact($filter);

        if ( $tag_it->getId() == '' ) return " AND 1 = 2 ";

        
        return  " AND EXISTS (SELECT 1 FROM BlogPostTag g " .
                "              WHERE g.Tag IN (".join(',', $tag_it->fieldToArray('TagId')).")".
                "                AND g.BlogPost = t.BlogPostId ) ";
    }
}
