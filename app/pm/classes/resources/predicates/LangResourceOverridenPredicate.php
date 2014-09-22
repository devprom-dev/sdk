<?php

class LangResourceOverridenPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return 'and overriden='.$filter;
 	}
} 
