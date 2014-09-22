<?php

class UserSystemRolePredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'admin':
 				return " AND t.IsAdmin = 'Y' ";
 		}
 	}
}
