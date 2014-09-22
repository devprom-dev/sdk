<?php

class CommonAccessObjectPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 		switch ( $filter )
 		{
 			case 'report':
 				return " AND t.ReferenceType = 'PMReport' ";

 			case 'entity':
 				return " AND t.ReferenceType = 'Y' ";

 			case 'attribute':
 				return " AND t.ReferenceType = 'A' ";
 				
 			case 'object':
 				return " AND t.ReferenceType = 'O' ";

 			case 'module':
 				return " AND t.ReferenceType = 'PMPluginModule' ";

 		}
 	}
}
