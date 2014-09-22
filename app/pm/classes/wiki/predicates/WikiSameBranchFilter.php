<?php

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class WikiSameBranchFilter extends FilterPredicate {

    function _predicate( $filter )
    {
        $parentPage = $filter->get('ParentPage');
        if ($parentPage) {
            return ' AND t.ParentPage = ' . $filter->get('ParentPage');
        } else {
            return '';
        }
    }

}