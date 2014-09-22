<?php

class ArtefactVersionFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
		return " AND t.Version = '".$filter."' ";
 	}
}
