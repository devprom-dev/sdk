<?php

class ChangeLogActionFilter extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'added':
 			case 'modified':
 			case 'deleted':
 			    return " AND t.ChangeKind = '".$filter."' ";

 			case 'commented':
 			    return " AND t.ChangeKind IN ('".$filter."', 'comment_modified', 'comment_deleted') ";
 			    
 			default:
 				return " AND 1 = 2 ";
 		}
 	}
}
