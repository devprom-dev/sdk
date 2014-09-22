<?php

class LangResourceTermPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		return 'and contains='.$filter;
 	}
} 
