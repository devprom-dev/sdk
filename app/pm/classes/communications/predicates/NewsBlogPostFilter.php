<?php

class NewsBlogPostFilter extends FilterPredicate
{
    function _predicate( $filter )
    {
        global $model_factory;
        	
        $post = $model_factory->getObject('BlogPost');

        $part_it = getSession()->getUserIt()->getParticipantIt();
        
        $policy = $model_factory->getAccessPolicy();
        
        $vpds = array();

        while ( !$part_it->end() )
        {
            $roles = $part_it->getRoles();

            if ( $policy->check_roles_access( ACCESS_READ, $post, null ) )
            {
                array_push( $vpds, $part_it->get('VPD') );
            }
            	
            $part_it->moveNext();
        }

        return " AND t.VPD IN ('".join($vpds, "','")."')";
    }
}