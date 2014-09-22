<?php

class PortfolioPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		$portfolio_it = getFactory()->getObject('Portfolio')->getExact($filter);
 		
 		if ( $portfolio_it->getId() == '' ) return " AND 1 = 2 ";
 		
 		return " AND t.VPD IN ('".join("','", $portfolio_it->getRef('LinkedProject')->fieldToArray('VPD'))."')";
 	}
}
