<?php

class BlogPostDateFilter extends FilterPredicate
{
    function _predicate( $filter )
    {
        list( $month, $year ) = preg_split('/-/', $filter);
        
        return " AND EXTRACT(YEAR FROM RecordCreated) = ".$year.
               " AND EXTRACT(MONTH FROM RecordCreated) = ".$month;
    }
}
