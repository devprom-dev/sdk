<?php

class CustomResourceKeyPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return 'AND t.ResourceKey = BINARY "'.$filter.'"';
 	}
} 
