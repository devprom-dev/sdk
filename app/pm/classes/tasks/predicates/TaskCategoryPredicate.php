<?php

class TaskCategoryPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'additional':
 				return " AND t.ChangeRequest IS NULL ";
 		}
 	}
}
