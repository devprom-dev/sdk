<?php

class BlogPostTagsPersister extends ObjectSQLPersister
{
    function getSelectColumns( $alias )
    {
        $columns = array();
        	
        array_push( $columns,
            "(SELECT GROUP_CONCAT(wt.BlogPostTagId) FROM BlogPostTag wt " .
            "  WHERE wt.BlogPost = ".$this->getPK($alias)." ) Tags " 
        );

        return $columns;
    }
}
